<?php

namespace App\Http\Controllers;

use App\Forum;
use App\Platform;
use App\Post;
use App\PostLike;
use App\PostReply;
use App\Topic;
use Exception;
use Illuminate\Http\Request;
use function Psy\debug;

class SeedForumController extends Controller
{
    private $platform;
    private $topic;
    private $forum;
    private $post;
    private $postReply;
    private $postLike;

    public function __construct(Platform $platform, Topic $topic, Forum $forum, Post $post, PostReply $postReply, PostLike $postLike)
    {
        $this->platform = $platform;
        $this->topic = $topic;
        $this->forum = $forum;
        $this->post = $post;
        $this->postReply = $postReply;
        $this->postLike = $postLike;
    }

    public function getForum(Request $request)
    {
        try {
            //$platform = $this->platform->find($request->platform_id); nunca usar
            $forum = $this->forum
                ->where('platform_id', $request->platform_id)
                ->with('image:id,filename')
                ->first();

            return response()->json([
                'status' => 'success',
                'forum' => $forum,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function getAllTopics(Request $request)
    {
        try {
            $forum = $this->forum
                ->where('platform_id', $request->platform_id)
                ->with('image:id,filename')
                ->first();
            $topics = $this->topic
                ->where('forum_id', $forum->id)
                ->where('active', 1)
                ->with('image:id,filename')
                ->withCount('posts_active')
                ->with('last_post')
                ->get();

            return response()->json([
                'status' => 'success',
                'topics' => $topics,
                'forum' => $forum,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function getPostsByTopicId(Request $request, $id)
    {
        try {
            $forum = $this->forum->where('platform_id', $request->platform_id)->first();
            $topic = $this->topic->where('id', $id)->first();
            $hasPost = $this->post->where('topic_id', $id)->where('approved', 1)->count();
            if ($hasPost != 0) {
                $topic->views++;
                $topic->save();
            } else {
                $topic->views = 0;
                $topic->save();
            }
            $order = $request->input('order_by');
            $posts = $this->post
                ->where('forum_id', $forum->id)
                ->where('topic_id', $id)
                ->where('approved', 1)
                ->with('topic:id,title')
                ->with('subscribers:id,name,thumb_id')
                ->withCount('replies')
                ->with('platforms_users:id,name,thumb_id')
                ->with('last_replies')
                ->when(($order == 'recently' || $order == ''), function ($query) {
                    return $query->orderBy('created_at', 'DESC');
                })
                ->when(($order == 'most_commented'), function ($query) {
                    return $query->orderBy('replies_count', 'DESC');
                })
                ->when(($order == 'most_viewed'), function ($query) {
                    return $query->orderBy('views', 'DESC');
                })
                ->get();

            return response()->json([
                'status' => 'success',
                'posts' => $posts,
                'topic' => $topic
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function getPostById(Request $request)
    {
        try {
            $forum = $this->forum->where('platform_id', $request->platform_id)->first();
            $post = $this->post
                ->where('forum_id', $forum->id)
                ->where('topic_id', $request->topic_id)
                ->where('id', $request->post_id)
                ->where('approved', 1)
                ->with('topic:id,title')
                ->with('subscribers:id,name,thumb_id')
                ->with('platforms_users:id,name,thumb_id')
                ->first();

            $post->views++;
            $post->save();

            $replies = $this->postReply
                ->where('post_id', $request->post_id)
                ->with('subscribers:id,name,thumb_id')
                ->with('platforms_users:id,name,thumb_id')
                ->get();

            return response()->json([
                'status' => 'success',
                'post' => $post,
                'totalReplies' => $replies->count(),
                'replies' => $replies,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function sendPost(Request $request)
    {
        try {
            if (!empty($request->tags)) {
                $tag_dump = implode(';', $request->tags);
                $request->request->add(['tags' => $tag_dump . ';']);
            } else {
                $request->request->add(['tags' => '']);
            }

            $subscriber_id = auth('api')->user()->id;
            $topic_id = $request->input('topic_id');
            $forum = $this->forum->where('platform_id', $request->platform_id)->get()->first();
            $topic = $this->topic->find($topic_id);

            $request->request->add([
                'body' => strip_tags($request->input('body'), '<p><i><b><u>'),
                'forum_id' => $forum->id,
                'subscribers_id' => $subscriber_id,
                'approved' => !$topic->moderation,
            ]);
            $this->post->create(
                $request->all()
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Parabéns! Sua postagem foi publicada com sucesso.',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function updatePost(Request $request)
    {
        try {
            $subscriber_id = auth('api')->user()->id;
            $topic_id = $request->input('topic_id');
            $post_id = $request->input('post_id');
            $forum = $this->forum->where('platform_id', $request->platform_id)->get()->first();

            $post = $this->post
                ->where('subscribers_id', $subscriber_id)
                ->where('topic_id', $topic_id)
                ->where('forum_id', $forum->id)
                ->where('id', $post_id);

            $post->update([
                'body' => strip_tags($request->input('body'), '<p><i><b><u>'),
                'title' => $request->input('title'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Parabéns! Sua postagem foi atualizada com sucesso.',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function postReply(Request $request)
    {
        try {
            $subscriber_id = auth('api')->user()->id;
            $request->request->add([
                'body' => strip_tags($request->body, '<p><i><b><u>'),
                'post_id' => $request->post_id,
                'subscribers_id' => $subscriber_id,
            ]);

            $reply = $this->postReply->create(
                $request->all()
            );

            $reply = $this->postReply
                ->where(['id' => $reply->id])
                ->with('subscribers')
                ->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Parabéns! Sua postagem foi publicada com sucesso.',
                'reply' => $reply,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getTopics(Request $request)
    {
        try {
            $forum = $this->forum->where('platform_id', $request->platform_id)->get()->first();
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
                'status' => 'success',
                'results' => $topics
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function likePostAndReply(Request $request)
    {

        try {
            $likes = 0;
            $hasLiked = false;

            $postId = $request->input('post_id');
            $replyId = $request->input('post_reply_id');
            $subscriberId = $request->input('subscribers_id');

            // Verifica se é post ou reply e se já foi dado like ou não
            $hasLiked = $this->postLike
                ->when($postId, function ($query, $postId) use ($subscriberId) {
                    return $query->where(['post_id' => $postId, 'subscribers_id' => $subscriberId]);
                })
                ->when($replyId, function ($query, $replyId) use ($subscriberId) {
                    return $query->where(['post_reply_id' => $replyId, 'subscribers_id' => $subscriberId]);
                })
                ->get()
                ->first();

            // Se o subscriber não deu like ainda
            if (!$hasLiked) {
                $this->postLike->create($request->all());
            }

            // Retorna a quantidade de like
            $likes = $this->postLike
                ->when($postId, function ($query, $postId) {
                    return $query->where('post_id', $postId);
                })
                ->when($replyId, function ($query, $replyId) {
                    return $query->where('post_reply_id', $replyId);
                })
                ->count();

            if ((!$hasLiked) && (!empty($postId))) {
                $post = $this->post->find($postId);
                $post->likes = $likes;
                $post->save();
            }

            if ((!$hasLiked) && (!empty($replyId))) {
                $postReply = $this->postReply->find($replyId);
                $postReply->likes = $likes;
                $postReply->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => ($hasLiked ? 'Você já curtiu esse post.' : 'Obrigado por cutir esse post.'),
                'likes' => $likes,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function deletePost(Request $request)
    {
        try {
            $subscriber_id = auth('api')->user()->id;
            $post_id = $request->input('post_id');
            $post_reply_id = $request->input('post_reply_id');

            $post = '';
            $postReply = '';

            if ($post_id) {
                $post = $this->post
                    ->where('id', $post_id)
                    ->where('subscribers_id', $subscriber_id)
                    ->first();
                $topic = $this->topic
                    ->where('id', $post->topic_id)
                    ->first();
                $topic->views -= $post->views;
                $topic->save();
                $post->delete();
            }

            if ($post_reply_id) {
                $postReply = $this->postReply
                    ->where('id', $post_reply_id)
                    ->where('subscribers_id', $subscriber_id)
                    ->first();
                $postReply->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Postagem removida com sucesso.',
                'post' => $post,
                'postReply' => $postReply,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }
}
