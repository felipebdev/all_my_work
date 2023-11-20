<?php

namespace App\Http\Controllers;

use App\File;
use App\Forum;
use App\Platform;
use App\PlatformUser;
use App\Post;
use App\PostLike;
use App\PostReply;
use App\Template;
use App\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Psy\debug;

class ForumPostController extends Controller
{
    private $forum;
    private $platform;
    private $template;
    private $topic;
    private $post;
    private $postReply;
    private $postLike;

    /**
     * @var PostReply
     */

    public function __construct(Forum $forum, Platform $platform, Template $template,
                                Topic $topic, Post $post, PostReply $postReply, PostLike $postLike)
    {
        $this->forum = $forum;
        $this->platform = $platform;
        $this->template = $template;
        $this->topic = $topic;
        $this->post = $post;
        $this->postReply = $postReply;
        $this->postLike = $postLike;
    }

    // Moderation View - Show pnly Approved posts
    public function postModerationAccepted(Request $request)
    {
        try {
            $approved = 1;
            $forum = $this->forum
                ->where('platform_id', Auth::user()->platform_id)
                ->get()
                ->first();

            $posts = $this->post
                ->where('forum_id', $forum->id)
                ->where('approved', $approved)
                ->with('subscribers:id,name,thumb_id')
                ->with('topic')
                ->get();
            return view('forum.moderation.index', compact('posts', 'approved'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Moderation View - Show only Pendding posts
    public function postModerationPending(Request $request)
    {
        try {
            $approved = 0;
            $forum = $this->forum
                ->where('platform_id', Auth::user()->platform_id)
                ->get()
                ->first();

            $posts = $this->post
                ->where('forum_id', $forum->id)
                ->where('approved', $approved)
                ->with('subscribers:id,name,thumb_id')
                ->with('topic')
                ->get();
            return view('forum.moderation.index', compact('posts', 'approved'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // EndPoint for shown the posts (platform only)
    public function getAllPosts(Request $request)
    {
        try {
            $forum = $this->forum
                ->where('platform_id', Auth::user()->platform_id)
                ->get()
                ->first();

            $posts = $this->post->select(['body as post', 'id', 'subscribers_id', 'topic_id', 'title', 'created_at'])
                ->where('forum_id', $forum->id)
                ->where('approved', $request->input('status'))
                ->with('subscribers:id,name,thumb_id')
                ->with('topic:id,title')
                ->get();

            return response()->json([
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // EndPoint for move to pending or approved (platform only)
    public function postModeration(Request $request)
    {
        try {
            $posts = $this->post->whereIn('id', $request->posts)->get();
            foreach ($posts as $post) {
                $post->approved = !($request->input('status') ? 1 : 0);
                $post->save();
            }
            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // EndPoint for delete post (platform only)
    public function postDelete(Request $request)
    {
        try {
            $posts = $this->post->whereIn('id', $request->posts)->get();
            foreach ($posts as $post) {
                $this->postLike->where('post_id', $post->id)->delete();
                $this->postReply->where('post_id', $post->id)->delete();
                $this->post->find($post->id)->delete();
            }
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // EndPoint for get post by ID (platform only)
    public function getRepliesByPostID(Request $request)
    {
        try {
            $replies = $this->postReply
                ->select(['body as post', 'id', 'subscribers_id', 'platforms_users_id', 'created_at', 'approved'])
                ->where(['post_id' => $request->input('post_id')])
                ->with('subscribers:id,name,thumb_id')
                ->with('platforms_users:id,name,thumb_id')
                ->get();
            return response()->json([
                'replies' => $replies
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // EndPoint for send reply to post (platform only)
    public function sendReplyPost(Request $request)
    {

        try {
            $post = $this->post->select()->where(['id' => $request->input('post_id')])->first();
            $this->postReply->create([
                'body' => $request->input('text'),
                'approved' => $post->approved,
                'post_id' => $post->id,
                'platforms_users_id' => Auth::user()->id
            ]);
            return response(['message' => 'Post criado com sucesso.'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // EndPoint for delete reply (platform only)
    public function deleteReplyPost(Request $request)
    {
        try {
            $reply = $this->postReply->select()->where(['id' => $request->input('reply_id')])->first();
            $reply->delete();
            return response('', 204);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // EndPoint for hidden/show reply (platform only)
    public function changeStatusReplyPost(Request $request)
    {
        try {
            $reply = $this->postReply->select()->where(['id' => $request->input('reply_id')])->first();
            $reply->approved = !$reply->approved;
            $reply->save();
            return response(['approved' => $reply->approved], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
