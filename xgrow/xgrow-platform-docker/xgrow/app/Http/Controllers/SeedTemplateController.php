<?php

namespace App\Http\Controllers;

use App\Category;
use App\Content;
use App\Events\UserAccessedSite;
use App\Menu;
use App\Platform;
use App\PlatformSiteConfig;
use App\Section;
use App\Widget;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class SeedTemplateController extends Controller
{
    private $section;
    private $content;
    private $platform;
    private $platformSiteConfig;
    private $menu;
    private $widget;
    private $category;

    public function __construct(Section $section, Content $content, Platform $platform, PlatformSiteConfig $platformSiteConfig, Menu $menu, Widget $widget, Category $category)
    {
        $this->section = $section;
        $this->content = $content;
        $this->platform = $platform;
        $this->platformSiteConfig = $platformSiteConfig;
        $this->menu = $menu;
        $this->widget = $widget;
        $this->category = $category;
    }

    public function seedConfig(Request $request)
    {

        $config = $this->platformSiteConfig->setUpTemplate($request['platform_id']);
        $template = $this->platform->find($request['platform_id'])->template;
        $info = $this->platform->select(['name'])->find($request['platform_id']);

        if ($config) {
            return response()->json([
                'status' => 'success',
                'template' => $template,
                'config' => $config,
                'info' => $info
            ]);
        }

        return response()->json(['error' => true, 'message' => 'No Config found']);
    }

    public function seedMenu(Request $request)
    {

        $menu = $this->menu->setUpMenu($request->platform_id);
        $menu = $this->menu->filterByCourseRestrictions($menu, auth('api')->user());
        $menu = $this->menu->filterBySectionRestrictions($menu, auth('api')->user());
        $menu = $this->menu->filterByContentRestrictions($menu, auth('api')->user());

        $accept_terms = auth('api')->user()->accept_terms;

        if ($menu) {
            return response()->json(['menu' => $menu, 'accept_terms' => $accept_terms], 200, array(), JSON_PRETTY_PRINT);
        }
        return response()->json(['error' => true]);

    }

    public function seedFooter(Request $request)
    {

        $platformSiteConfig = $this->platformSiteConfig->where('platform_id', $request->platform_id)->first();

        $config['logo_rodape_filename'] = ($platformSiteConfig->image_logo_rodape_id != null) ? $platformSiteConfig->image_logo_rodape->filename : '';
        $config['copyright'] = $platformSiteConfig->copyright;

        if ($config) {
            return response()->json([
                'status' => 'success',
                'config' => $config
            ]);
        }

        return response()->json(['error' => true, 'message' => 'No Config found']);

    }

    public function seedWelcome(Request $request)
    {

        $widgets = $this->widget->setUpWidget($request->platform_id);
        $widgets = $this->widget->filterByCourseRestrictions($widgets, auth('api')->user());
        $widgets = $this->widget->filterBySectionRestrictions($widgets, auth('api')->user());
        $widgets = $this->widget->filterByContentRestrictions($widgets, auth('api')->user());

        if ($widgets) {

            UserAccessedSite::dispatch(auth('api')->user());

            return response()->json(
                [
                    'widgets' => $widgets,
                ], 200, array(), JSON_PRETTY_PRINT
            );
        }
        return response()->json(['error' => true, 'message' => 'No items found']);

    }

    public function seedSection(Request $request)
    {

        $rules['section_key'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()]);
        }

        $section = Section::where('platform_id', $request->platform_id)
            ->where('section_key', $request->section_key)
            ->get();
        if($section) {
            return response()->json([
                'section' => $section
            ]);
        }
        return response()->json(['error' => true, 'message' => 'Section not found']);
    }

    public function seedContent(Request $request)
    {
        try{
            $content = Content::findBySlug($request->url, ['id']);
            if($content) {
                $contents = DB::table('sections')
                    ->join('contents', 'sections.id', '=', 'contents.section_id')
                    ->where('sections.platform_id', $request->platform_id)
                    ->where('contents.id', $content->id)
                    ->select('contents.*')
                    ->get();

                return response()->json([
                    'content' => $contents
                ]);
            }
            return response()->json(['error' => true, 'message' => 'Content not found']);
        } catch (Exception $e){
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }
    //Transferido para SeedSectionController
    public function seedAllContents(Request $request)
    {
        try{
            $platform = $this->platform->find($request->platform_id);
            if($platform) {
                $contents = $platform->contents()
                                ->with('thumb_small')
                                ->with('thumb_big')
                                ->where('is_course', 0)
                                ->limit($request->amount)->get();

                return response()->json([
                    'contents' => $contents
                ]);

            }
            return response()->json(['error' => true, 'message' => 'Content not found']);
        } catch (Exception $e){
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }

    public function seedFeatureByOrder(Request $request)
    {
        try{
            $section = $this->section->where('section_key', $request->section_key)->first();
            if($section) {
                $content = $this->content
                                ->where('featured_order', $request->feature_order)
                                ->where('section_id', $section->id)
                                ->with('thumb_small')
                                ->with('thumb_big')
                                ->first();

                if($content == null){
                    $content = $this->content
                                ->where('section_id', $section->id)
                                ->with('thumb_small')
                                ->with('thumb_big')
                                ->skip($request->feature_order-1)
                                ->first();
                }

                return response()->json([
                    'content' => $content
                ]);
            }
            return response()->json(['error' => true, 'message' => $request->section_key]);
        } catch (Exception $e){
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }

    public function seedContentsFromTheFeatureOrder(Request $request)
    {
        try{
            $section = $this->section->where('section_key', $request->section_key)->first();
            if($section) {

                $content = $this->content
                                ->where('section_id', $section->id)
                                ->with('thumb_small')
                                ->with('thumb_big')
                                ->skip($request->feature_order-1)
                                ->limit($request->limit)
                                ->get();

                return response()->json([
                    'content' => $content
                ]);
            }
            return response()->json(['error' => true, 'message' => $request->section_key]);
        } catch (Exception $e){
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }


    public function getContentByCategory(Request $request)
    {

        try{

            $contents = $this->category
                            ->listMixContent($request->id);

            $category = $this->category->select('name')->find($request->id);

            $data = [
                'name' => $category->name,
                'contents' => $contents
            ];

            return response()->json($data, 200, array(), JSON_PRETTY_PRINT);


        } catch (Exception $e){
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }




}
