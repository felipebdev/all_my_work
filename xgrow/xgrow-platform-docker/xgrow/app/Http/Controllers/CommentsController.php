<?php

namespace App\Http\Controllers;

use App\Author;
use App\Comment;
use App\Content;
use App\Course;
use App\Platform;
use App\Section;
use App\Subscriber;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentsController extends Controller
{
    private $platform;
    private $course;
    private $section;
    private $author;
    private $comment;
    private $content;
    private $subscriber;

    public function __construct(Content $content, Course $course, Section $section, Subscriber $subscriber, Comment $comment, Author $author, Platform $platform)
    {
        $this->content = $content;
        $this->course = $course;
        $this->section = $section;
        $this->subscriber = $subscriber;
        $this->comment = $comment;
        $this->author = $author;
        $this->platform = $platform;
    }

    public function store(Request $request)
    {
        $content = $this->content->find($request->content_id);

        $comment = $content->comments()->create($request->all());

        return response()->json(['status' => 'success', 'data' => $comment, 'message' => '']);
    }

    public function index(Request $request)
    {
        $platform_id = Auth::user()->platform_id;

        $data = $this->getDataManage($platform_id, 1);

        $platform = $this->platform->find($platform_id);

        $data['approved'] = 1;
        $data['approve_comments'] = $platform->platformSiteConfig->approve_comments ?? null;

        return view('comments.index', $data);
    }

    public function pedding(Request $request)
    {
        $platform_id = Auth::user()->platform_id;

        $data = $this->getDataManage($platform_id, 0);

        $data['approved'] = 0;

        $platform = $this->platform->find($platform_id);
        $data['approve_comments'] = $platform->platformSiteConfig->approve_comments ?? null;

        return view('comments.index', $data);
    }


    public function destroy(Request $request)
    {
        try {
            $comment = $this->comment->find($request->comment_id);
            $comment->delete();
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function approved(Request $request)
    {
        try {
            $comment = $this->comment->find($request->comment_id);
            $comment->update([
                'approved' => $request->status,
            ]);
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    public function setApproveComments(Request $request)
    {
        try {
            $platform_id = Auth::user()->platform_id;
            $platform = $this->platform->find($platform_id);
            $platformSiteConfig = $platform->platformSiteConfig;

            if (is_null($platformSiteConfig)) {
                return response()->json(['status' => 'error', 'message' => 'Algo de errado aconteceu. Contate o suporte!']);
            }

            $platformSiteConfig->update(['approve_comments' => $request->approve_comments]);

            return response()->json(['status' => $request->approve_comments]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function changeStatusSelected(Request $request)
    {
        try {
            $change = $request->status ? 0 : 1;
            foreach ($request->comments as $value) {
                $comment = $this->comment->find($value);
                $comment->update([
                    'approved' => $change,
                ]);
            }
            return response()->json(['data' => 'Os comentários form movidos com sucesso']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteSelected(Request $request)
    {
        try {
            foreach ($request->comments as $value) {
                $comment = $this->comment->find($value);
                $comment->delete();
            }
            return response()->json(['data' => 'Os comentários form excluídos com sucesso']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function manage(Request $request)
    {
        try {

            $platform_id = Auth::user()->platform_id;

            $comments = $this->comment
                ->with(['content' => function ($query) {
                    $query->with('thumb_small:id,filename');
                }])
                ->with(['subscriber' => function ($query) {
                    $query->with('thumb:id,filename');
                }])
                ->with(['platform_user' => function ($query) {
                    $query->with('thumb:id,filename');
                }])
                ->where('platform_id', $platform_id)
                ->where('approved', $request->approved);


            $comments = $this->setFilter($comments, $request);


            $Totalcomments = $comments->count();

            $replies = [];

            //Aprovados
            /*
            if($request->approved == 1){
                //Exibir só após o filtro
                $replies =$this->comment
                            ->with(['content' => function($query){
                                $query->with('thumb_small:id,filename');
                            }])
                             ->with(['subscriber' => function($query){
                                $query->with('thumb:id,filename');
                             }])
                             ->with(['platform_user' => function($query){
                                $query->with('thumb:id,filename');
                             }])
                             ->where('id_comment_sub','<>',0)
                             ->where('approved', $request->approved);

                $replies = $this->setFilter($replies, $request);

                $replies = $replies->get();

                $comments = $comments->where('id_comment_sub', null)->get();
            }
            else{
                //Não aprovados
                $comments = $comments->get();
            }
            */

            $comments = $comments->orderBy('comment_id', 'ASC')->orderBy('id_comment_sub', 'ASC')->get();


            return response()->json([
                'comments' => $comments,
                'replies' => $replies,
                'totalComments' => $Totalcomments
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);

        }
    }


    private function setFilter($comments, $request)
    {

        if ($request->subscriber_id != '') {
            $comments = $comments->where('subscriber_id', $request->subscriber_id);
        }

        if ($request->section_id != '') {
            $comments = $comments->whereHas('content', function ($query) use ($request) {
                $query->where('section_id', $request->section_id);
            });
        }

        if ($request->author_id != '') {
            $comments = $comments->whereHas('content', function ($query) use ($request) {
                $query->where('author_id', $request->author_id);
            });
        }

        if ($request->course_id != '') {
            $comments = $comments->whereHas('content', function ($query) use ($request) {
                $query->where('course_id', $request->course_id);
            });
        }

        return $comments;
    }

    public function sendComment(Request $request)
    {

        try {

            $platform_id = Auth::user()->platform_id;

            $content = $this->content->find($request->contents_id);

            $comment_sub = $this->comment->find($request->id_comment_sub);

            $comment = $content->comments()->create([
                'platform_id' => $platform_id,
                'subscriber_type' => 'platform_user',
                'platform_user_id' => Auth::user()->id,
                'text' => $request->text,
                'id_comment_sub' => $request->id_comment_sub,
                'contents_id' => $request->contents_id,
                'comment_id' => $comment_sub->comment_id,
                'approved' => 1,
            ]);

            $data = DB::table('comments')->select('comments.*', 'platforms_users.name')
                ->join('platforms_users', 'comments.platform_user_id', '=', 'platforms_users.id')
                ->where('comments.contents_id', $request->contents_id)
                ->where('comments.id', $comment->id)->first();

            $commentRow = $data;

            return response()->json(['status' => 'success', 'data' => $commentRow, 'message' => '']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

    }

    private function getDataManage($platform_id, $approved)
    {
        $courses = $this->course->where('platform_id', $platform_id)
            ->pluck('name', 'id')->toArray();
        $data['courses'] = array('' => 'Curso') + $courses;

        $sections = $this->section->where('platform_id', $platform_id)
            ->pluck('name', 'id')->toArray();
        $data['sections'] = array('' => 'Seção') + $sections;

        $authors = $this->author->where('platform_id', $platform_id)
            ->pluck('name_author', 'id')->toArray();
        $data['authors'] = array('' => 'Autor') + $authors;


        $data['subscribers'] = $this->subscriber
            ->join('comments', 'comments.subscriber_id', '=', 'subscribers.id')
            ->where('subscribers.platform_id', $platform_id)
            ->where('comments.approved', $approved)
            ->groupBy('subscribers.name')
            ->orderBy('subscribers.name', 'ASC')
            ->select('subscribers.id', 'name as value')->get();


        $data['total_approved'] = $this->comment->where('platform_id', $platform_id)->where('approved', 1)->count();
        $data['total_not_approved'] = $this->comment->where('platform_id', $platform_id)->where('approved', 0)->count();

        return $data;
    }

    public function subscribers()
    {

        $subscribers = $this->subscriber
            ->select('id', 'name as value')->limit(2)->get();
        /*
         return str_replace(array('[', ']'), '', htmlspecialchars(json_encode($subscribers), ENT_NOQUOTES));
 */


    }

    public function addCommentId($id_comment_sub = 0)
    {

        $comments = $this->comment->where('id_comment_sub', null)->get();

        $replies = $this->comment->where('id_comment_sub', '<>', 0)->get();

        foreach ($comments as $comment) {
            $this->createRowComment($comment, $replies, $comment->id);
        }

    }

    function createRowComment($comment, $replies, $comment_id)
    {
        echo "<ul>{$comment->id} - {$comment->id_comment_sub} - {$comment_id}";
        $comment->update(['comment_id' => $comment_id]);
        foreach ($replies as $reply) {
            if ($reply->id_comment_sub == $comment->id) {
                $this->createRowComment($reply, $replies, $comment_id);
            }
        }
        echo "</ul>";
    }

    public function getAllComments(Request $request)
    {
        try {
            $comments = $this->comment->getAllComments(Auth::user()->platform_id, $request->status);
            return response()->json([
                'data' => $comments,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getComment(Request $request)
    {
        try {
            $comment = $this->comment
                ->where(['platform_id' => Auth::user()->platform_id, 'id' => $request->input('id')])
                ->with('subscriber:id,name')
                ->with('content:id,title')
                ->first();
            return response()->json([
                'comment' => $comment,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function sendReplyComment(Request $request)
    {
        try {
            $comment = $this->comment->find($request->input('comment_id'));
            $comment_reply = $this->comment->create([
                'platform_id' => Auth::user()->platform_id,
                'subscriber_type' => 'platform_user',
                'platform_user_id' => Auth::user()->id,
                'text' => $request->input('text'),
                'id_comment_sub' => $request->input('comment_id'),
                'contents_id' => $comment->contents_id,
                'approved' => 1,
            ]);
            $comment_reply->comment_id = $comment_reply->id;
            $comment_reply->commentable_type = 'App\Content';
            $comment_reply->save();

            return response()->json([
                'data' => $comment_reply->id,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getRepliesByCommentId(Request $request)
    {
        try {
            $comments = $this->comment
                ->where(['platform_id' => Auth::user()->platform_id, 'id_comment_sub' => $request->input('comment_id')])
                ->with('subscriber:id,name')
                ->with('content:id,title')
                ->orderBy('updated_at', 'DESC')
                ->get();
            return response()->json([
                'comments' => $comments,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function hiddenComment(Request $request){
        try {
            $comment = $this->comment
                ->where(['platform_id' => Auth::user()->platform_id, 'id' => $request->input('comment_id')])
                ->with('subscriber:id,name')
                ->with('content:id,title')
                ->first();
            $comment->approved = !$comment->approved;
            $comment->save();

            return response()->json([
                'approved' => $comment->approved ,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function deleteComment(Request $request){
        try {
            $comment = $this->comment
                ->where(['platform_id' => Auth::user()->platform_id, 'id' => $request->input('comment_id')])
                ->with('subscriber:id,name')
                ->with('content:id,title')
                ->first();
            $commentID = $comment->id;
            $comment->delete();
            return response()->json([
                'comment' => $commentID,
                'status' => 'true',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }
}
