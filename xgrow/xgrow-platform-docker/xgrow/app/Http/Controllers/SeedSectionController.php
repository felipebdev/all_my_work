<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentLog;
use App\Events\UserAccessedSection;
use App\Platform;
use App\Section;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use stdClass;

class SeedSectionController extends Controller
{
    private $section;
    private $content;
    private $contentLog;
    private $platform;

    public function __construct(Section $section, Content $content, Platform $platform, ContentLog $contentLog)
    {
        $this->section = $section;
        $this->content = $content;
        $this->platform = $platform;
        $this->contentLog = $contentLog;
    }

    //pega as configurações na aparição dos conteudos
    public function seedSectionConfig(Request $request)
    {
        try {

            $config = DB::table('sections')
                ->join('templates', 'sections.template_id', '=', 'templates.id')
                ->select('sections.content_title', 'sections.content_author', 'sections.content_subtitle', 'sections.content_description', 'sections.qtd_per_page', 'sections.allow_comments', 'sections.allow_likes', 'templates.folder')
                ->when($request->id, function ($query) use ($request) {
                    $query->where('sections.id', $request->id);
                })
                ->when($request->section_key, function ($query) use ($request) {
                    $query->where('section_key', $request->section_key);
                })
                ->first();

            if ($config) {
                return response()->json([
                    'sectionConfig' => $config
                ]);
            }
            return response()->json(['error' => true, 'message' => 'Section not found']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }

    }


    //pega os conteudos mais recentes da seção
    public function seedLatestSectionContent(Request $request)
    {
        try {
            $section = $this->section
                ->when($request->id, function ($query) use ($request) {
                    $query->where('id', $request->id);
                })
                ->when($request->section_key, function ($query) use ($request) {
                    $query->where('section_key', $request->section_key);
                })
                ->first();
            if ($section) {

                $content = $this->content
                    ->where('section_id', $section->id)
                    ->where('published', 1)
                    ->with('thumb_small:id,filename')
                    ->with('thumb_big:id,filename')
                    ->latest()
                    ->limit($request->limit)
                    ->get();

                return response()->json([
                    'contents' => $content
                ]);
            }
            return response()->json(['error' => true, 'message' => $request->section_key]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }


    //pega todas as seções da plataforma
    public function seedAllSections(Request $request)
    {
        try {
            $platform = $this->platform->find($request->platform_id);
            if ($platform) {

                $sections = $this->section
                    ->where('platform_id', $request->platform_id)
                    ->where('active', 1)
                    ->with('thumb:id,filename')
                    ->with('image:id,filename')
                    ->get();

                return response()->json([
                    'sections' => $sections
                ]);
            }
            return response()->json(['error' => true, 'message' => 'Sections not found']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    //pega Xs conteudos cadastrados na plataforma
    public function seedAllContents(Request $request)
    {
        try {
            $platform = $this->platform->find($request->platform_id);
            if ($platform) {

                $contents = $this->content
                    ->join('sections', 'contents.section_id', '=', 'sections.id')
                    ->join('authors', 'contents.author_id', '=', 'authors.id')
                    ->join('platforms', 'sections.platform_id', '=', 'platforms.id')
                    ->where('platforms.id', $request->platform_id)
                    ->where('contents.published', 1)
                    ->where('contents.is_course', 0)
                    ->leftJoin('files', 'contents.thumb_small_id', '=', 'files.id')
                    ->select('contents.id', 'subtitle', 'has_video_link', 'published_at', 'title', 'sections.name_slug', 'sections.template_id', 'published', 'authors.name_author', 'files.filename', 'files.copyright')
                    ->limit($request->amount)->get();

                return response()->json([
                    'contents' => $contents
                ]);
            }
            return response()->json(['error' => true, 'message' => 'Content not found']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function seedContentsFromSection(Request $request)
    {
        try {
            $section = $this->section
                ->when($request->id, function ($query) use ($request) {
                    $query->where('id', $request->id);
                })
                ->when($request->section_key, function ($query) use ($request) {
                    $query->where('section_key', $request->section_key);
                })
                ->first();

            if ($section) {

                $contents = $this->content
                    ->where('section_id', $section->id)
                    ->where('published', 1);

                if ($request->different != 0)
                    $contents = $contents->where('id', '<>', $request->different);

                $contents = $contents
                    ->with('thumb_small:id,filename,copyright')
                    ->with('thumb_big:id,filename,copyright')
                    ->with('section:id,name_slug,template_id')
                    ->with('author:id,name_author');
                // ->skip($request->feature_order-1);

                if ($request->limit != 0)
                    $contents = $contents->limit($request->limit);

                $contents = $contents->get();

                //alteração contents
                return response()->json([
                    'contents' => $contents
                ]);
            }
            return response()->json(['error' => true, 'message' => "Seção não encontrada"]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }

    //pega conteudo especificamente pela featured_order escolhida
    public function seedFeatureByOrder(Request $request)
    {
        try {
            $section = $this->section
                ->when($request->id, function ($query) use ($request) {
                    $query->where('id', $request->id);
                })
                ->when($request->section_key, function ($query) use ($request) {
                    $query->where('section_key', $request->section_key);
                })
                ->first();

            if ($section) {
                //verifica se usuário tem permissão para acessar conteúdo
                if (Section::havePermission($section->id, auth('api')->user()->id)) {

                    $content = $this->content
                        ->where('featured_order', $request->feature_order)
                        ->where('section_id', $section->id)
                        ->where('published', 1)
                        ->with('thumb_small:id,filename,copyright')
                        ->with('thumb_big:id,filename,copyright')
                        ->with('section:id,name_slug,template_id')
                        ->with('author:id,name_author')
                        ->first();
                    $is_featured = 1;

                    //não refatora senão não funciona
                    //tem que puxar de this-content
                    if ($content == null) {

                        $content = $this->section->when($request->id, function ($query) use ($request) {
                            $query->where('id', $request->id);
                        })
                            ->when($request->section_key, function ($query) use ($request) {
                                $query->where('section_key', $request->section_key);
                            })
                            ->with('image:id,filename,copyright')
                            ->first();
                        // $content = $this->content
                        //             ->where('section_id', $section->id)
                        //             ->with('thumb_small:id,filename')
                        //             ->with('thumb_big:id,filename')
                        //             ->with('section:id,name_slug,template_id')
                        //             ->with('author:id,name_author')
                        //             ->skip($request->feature_order-1)
                        //             ->first();
                        $is_featured = 0;
                    }

                    $config = $this->sectionConfig($section->section_key);

                    return response()->json([
                        'content' => $content,
                        'config' => $config,
                        'is_featured' => $is_featured
                    ]);
                }
            }
            return response()->json(['error' => true, 'message' => $request->section_key]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }

    //pega os conteudos a partir na ordem após um destaque
    public function seedContentsFromTheFeatureOrder(Request $request)
    {
        try {
            $section = $this->section
                ->when($request->id, function ($query) use ($request) {
                    $query->where('id', $request->id);
                })
                ->when($request->section_key, function ($query) use ($request) {
                    $query->where('section_key', $request->section_key);
                })
                ->first();
            if ($section) {
                $order = $section->orderby_id;
                $order_array = $order_array = Section::orderTypes();

                $content = $this->content
                    ->where('section_id', $section->id)
                    ->where('published', 1)
                    ->with('thumb_small:id,filename,copyright')
                    ->with('thumb_big:id,filename,copyright')
                    ->with('section:id,name_slug,template_id')
                    ->with('author:id,name_author')
                    ->skip($request->feature_order - 1)
                    ->limit($request->limit)
                    ->orderBy('is_featured', 'DESC')
                    ->orderBy('featured_order', 'ASC')
                    ->orderBy($order_array[$order]['param'], $order_array[$order]['type'])
                    ->get();

                $config = $this->sectionConfig($section->section_key);

                if (isset($request->section_key) && isset($request->url_access)) {
                    if (!preg_match('/content/', $request->url_access)) {
                        $user = auth('api')->user();

                        // Finaliza o conteúdo
                        $this->contentLog->updateContentLog($user->id);

                        $data = [
                            'user_id' => $user->id,
                            'user_type' => "subscribers",
                            'platform_id' => $user->platform_id,
                            'ip' => $_SERVER["REMOTE_ADDR"],
                            'route' => $request->url_access,
                            'section_id' => 0,
                            'section_key' => $request->section_key,
                            'content_id' => 0,
                            'course_id' => 0
                        ];

                        ContentLog::create($data);
                    }
                }

                UserAccessedSection::dispatch($user, $section);

                return response()->json([
                    'contents' => $content,
                    'config' => $config
                ]);
            }
            return response()->json(['error' => true, 'message' => $request->section_key]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }

    //action de comentários transferido para conteúdo

    private function sectionConfig($section_key)
    {
        $config = DB::table('sections')
            ->select('content_title', 'content_author', 'content_subtitle', 'content_description', 'qtd_per_page', 'allow_comments', 'allow_likes')
            ->where('section_key', $section_key)
            ->first();

        return $config;
    }

}
