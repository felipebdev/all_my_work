<?php

namespace App\Http\Controllers;

use App\File;
use App\Forum;
use App\Http\Controllers\Controller;
use App\Platform;
use App\Template;
use App\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function JmesPath\search;

class ForumTopicController extends Controller
{
    private $forum;
    private $platform;
    private $template;
    private $topic;

    public function __construct(Forum $forum, Platform $platform, Template $template, Topic $topic)
    {
        $this->forum = $forum;
        $this->platform = $platform;
        $this->template = $template;
        $this->topic = $topic;
    }

    public function create()
    {
        try {
            $topic = new Topic;
            $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();
            $templates = $this->template->where('active', 1)->where('platform', 0)->get();

            return view('forum.topic.create', compact('forum', 'templates', 'topic'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|max:255',
                'description' => 'required',
            ]);

            if (!empty($request['tags'])) {
                $tag_dump = '';
                foreach ($request['tags'] as $tag) {
                    $tag_dump .= $tag . ';';
                }
                $request->request->add(['tags' => $tag_dump]);
            }

            $image = File::setUploadedFile($request, 'image');
            $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();
            $request->request->add([
                'description' => $request->input('description'),
                'forum_id' => $forum->id,
                'active' => ($request->input('active') ? 1 : 0),
                'moderation' => ($request->input('moderation') ? 1 : 0),
            ]);
            $topic = $this->topic->create(
                $request->all()
            );
            File::saveUploadedFile($topic, $image, 'image_id');

            return redirect()->route('forum.index');
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $topic = $this->topic->find($id);
            $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();

            // Verifica se o usuário tem permissão de acessar esse post
            if ($topic->forum_id != $forum->id) {
                return redirect()->route('forum.index')->with('error', 'Você não tem permissão para acessar esse topico.');
            }

            return view('forum.topic.create', compact('topic'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $topic = $this->topic->find($id);
            $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();

            // Verifica se o usuário tem permissão de acessar esse post
            if ($topic->forum_id != $forum->id) {
                return redirect()->route('forum.index')->with('error', 'Você não tem permissão para acessar esse topico.');
            }

            if (!empty($request['tags'])) {
                $tag_dump = '';
                foreach ($request['tags'] as $tag) {
                    $tag_dump .= $tag . ';';
                }
                $request->request->add(['tags' => $tag_dump]);
            }

            $image = File::setUploadedFile($request, 'image');

            $request->request->add([
                'active' => ($request->input('active') ? 1 : 0),
                'moderation' => ($request->input('moderation') ? 1 : 0),
            ]);
            $topic->update($request->all());
            File::saveUploadedFile($topic, $image, 'image_id');

            return redirect()->route('forum.index');
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getTopics(Request $request)
    {
        try {
            $forum = $this->forum->where('platform_id', Auth::user()->platform_id)->get()->first();
            if (!empty($request['search'])) {
                $topics = $this->topic
                    ->select('id', 'title as text')
                    ->where('forum_id', $forum->id)
                    ->where('title', 'like', "%" . $request['search'] . "%")
                    ->get();
            } else {
                $topics = $this->topic->select('id', 'title as text')->where('forum_id', $forum->id)->get();
            }
            return response()->json([
                'results' => $topics
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
