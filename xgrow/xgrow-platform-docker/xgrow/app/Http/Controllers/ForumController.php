<?php

namespace App\Http\Controllers;

use App\File;
use App\Forum;
use App\Menu;
use App\Platform;
use App\Post;
use App\Template;
use App\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    private $forum;
    private $platform;
    private $template;
    private $topic;
    private $post;
    private $menu;

    public function __construct(Forum $forum, Platform $platform, Template $template, Topic $topic, Post $post, Menu $menu)
    {
        $this->forum = $forum;
        $this->platform = $platform;
        $this->template = $template;
        $this->topic = $topic;
        $this->post = $post;
        $this->menu = $menu;
    }

    public function index()
    {
        try {
            $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();
            if (!$forum) {
                $forum_obj = new Forum;
                $forum_obj->platform_id = Auth::user()->platform_id;
                $forum_obj->save();

                /* Cria o CSS default: Dark */
                $slug = $this->platform->select('name_slug')->where('id', Auth::user()->platform_id)->get()->first();
                //createForumThemeConfig('theme.css', $slug->name_slug, null, 'dark');
                $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();
            }
            $topics = $this->topic->where('forum_id', $forum->id)->get();
            $posts = $this->post->where('forum_id', $forum->id)->get();
            $templates = $this->template->where('active', 1)->where('platform', 0)->get();

            return view('forum.index', compact('forum', 'templates', 'topics', 'posts'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $image = File::setUploadedFile($request, 'image');
            $thumb = File::setUploadedFile($request, 'thumb');

            $platform_id = Auth::user()->platform_id;
            $active = $request->input('active') ? 1 : 0;

            $request->request->add([
                'platform_id' => $platform_id,
                'active' => $active,
                'theme' => ($request->input('theme') ? 1 : 0),
            ]);
            $forum = $this->forum->updateOrCreate(
                ['id' => $request->id],
                $request->all()
            );
            File::saveUploadedFile($forum, $image, 'image_id');
            File::saveUploadedFile($forum, $thumb, 'thumb_id');
            $slug = $this->platform->select('name_slug')->where('id', $platform_id)->get()->first();
            $style = $forum->theme ? 'light' : 'dark';
            //createForumThemeConfig('theme.css', $slug->name_slug, null, $style);

            $items = $this->menu
                ->where(['platform_id' => $platform_id])
                ->where(['visible' => 1])
                ->orderBy('order', 'ASC')
                ->get();

            $hasForumMenu = $this->menu
                ->where(['platform_id' => $platform_id])
                ->where(['item_type' => 5])
                ->orderBy('order', 'ASC')
                ->first();


            if (!$hasForumMenu) {
                $order = count($items) + 1;
                DB::table('menus')
                    ->insert([
                        'item_type' => 5,
                        'item_id' => 0,
                        'order' => $order,
                        'platform_id' => $platform_id,
                        'visible' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                DB::table('menus')
                    ->where(['id' => $hasForumMenu->id])
                    ->update(['visible' => $active]);
            }

            return redirect()->route('forum.index');
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function isActive()
    {
        try {
            $forum = Forum::where('platform_id', Auth::user()->platform_id)->first();
            $active = $forum ? $forum->active : false;
            $resource = $forum ? $forum->id : 0;
            return response()->json(['error' => false, 'data' => ['status' => (bool)$active, 'resource' => $resource]]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
