<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Content;
use App\ContentLog;
use App\ContentView;
use App\Course;
use App\Events\UserAccessedContent;
use App\Like;
use App\Platform;
use App\Template;
use App\View;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeedContentController extends Controller
{
    private $content;
    private $contentLog;
    private $like;
    private $template;
    private $views;
    private $comment;
    private $platform;
    private $contentView;
    private $course;

    public function __construct(Content $content, Like $like, Template $template, View $view, Comment $comment, ContentLog $contentLog, Platform $platform, ContentView $contentView, Course $course)
    {
        $this->content = $content;
        $this->like = $like;
        $this->template = $template;
        $this->views = $view;
        $this->comment = $comment;
        $this->contentLog = $contentLog;
        $this->platform = $platform;
        $this->contentView = $contentView;
        $this->course = $course;
    }

    public function seedContentById(Request $request)
    {
        try {

            //verifica se usuário tem permissão para acessar conteúdo
            if (Content::havePermission($request->id, auth('api')->user()->id)) {

                $content = $this->content
                    ->where('contents.id', $request->id)
                    ->with('thumb_small:id,filename')
                    ->with('thumb_big:id,filename')
                    ->with('attachs')
                    ->with('author:id,name_author,author_photo')
                    ->first();

                //checa se conteúdo correponde a plataforma
                $this->checkContent($content->section->platform_id);

                if ($content) {

                    $content['content_model'] = ($content->section->content_template_id > 0) ? $this->template->find($content->section->content_template_id)->content_model : 0;

                    if (isset($request->id)) {
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
                            'section_key' => 0,
                            'content_id' => $request->id,
                            'course_id' => 0
                        ];

                        ContentLog::create($data);
                    }

                    UserAccessedContent::dispatch($user, $content);

                    $config = $this->sectionConfig($content->section_id);

                    return response()->json([
                        'content' => $content,
                        'config' => $config
                    ]);
                }
                return response()->json(['error' => true, 'message' => 'Content not found']);
            } else {
                return response()->json(['status' => 'Unauthorized user'], 401);
            }

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function seedContentLikes(Request $request)
    {
        $status = 0;
        if ($request->status == 1) {
            $status = 1;
        }
        try {
            //adiciona o like na tabela
            $like = $this->like->updateOrCreate(
                [
                    'subscriber_id' => auth('api')->user()->id, 'content_id' => $request->id
                ],
                [
                    'status' => $status
                ]
            );

            $content = $this->content->find($request->id);
            if ($request->status == 1) {
                $return = $content->update(['likes' => $content->likes++]);
            } else {
                $return = $content->update(['likes' => $content->likes--]);
            }
            return response()->json(['status' => 'success', 'data' => $return, 'message' => '']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    //recupera o like do usuario para a pagina de conteudo
    public function getContentLike(Request $request)
    {
        $like = DB::table('likes')->select('status')->where([
            ['subscriber_id', '=', auth('api')->user()->id],
            ['content_id', '=', $request->id]
        ])->count();

        $totalLikes = DB::table('likes')->select('status')->where([
            ['content_id', '=', $request->id],
            ['status', '=', 1]
        ])->count();
        if ($like != 0) {
            return response()->json(['status' => 'success', 'data' => "true", 'totalLikes' => $totalLikes]);
        } else {
            return response()->json(['status' => 'error', 'data' => "false"]);
        }
    }

    public function seedContentViews(Request $request)
    {
        try {
            $content = $this->content->find($request->id);
            $content->views++;
            $return = $content->save();


            $this->contentView->create([
                'content_id' => $request->id,
            ]);

            $this->views->create([
                'content_id' => $request->id,
                'subscriber_id' => auth('api')->user(),
            ]);
            return response()->json(['status' => 'success', 'data' => $return, 'message' => '']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getResearchContent(Request $request)
    {
        try {
            $contents = $this->content
                ->join('sections', 'contents.section_id', '=', 'sections.id')
                ->join('authors', 'contents.author_id', '=', 'authors.id')
                ->join('platforms', 'sections.platform_id', '=', 'platforms.id')
                ->where('platforms.id', $request->platform_id)
                ->where('contents.title', 'like', '%' . $request->text . '%')
                ->where('contents.published', 1)
                ->where('contents.is_course', 0)
                ->leftJoin('files', 'contents.thumb_small_id', '=', 'files.id')
                ->select('contents.*', 'sections.name', 'sections.name_slug', 'files.filename', 'authors.name_author')
                ->get();

            $restrict_content = [];
            foreach ($contents as $key => $content) {
                if($this->content->havePermission($content->id, auth('api')->user()->id)){
                    $restrict_content[] = $contents[$key];
                }
            }

            $courses = DB::table('courses')
                ->where('platform_id', $request->platform_id)
                ->where('active', 1)
                ->where('name', 'like', '%' . $request->text . '%')
                ->where('paid', '=', 0)
                ->leftJoin('files', 'courses.thumb_id', '=', 'files.id')
                ->select('courses.*', 'files.filename')
                ->get();

            $restrict_course = [];
            foreach ($courses as $key => $course) {
                if($this->course->havePermission($course->id, auth('api')->user()->id)){
                    $restrict_course[] = $courses[$key];
                }
            }


            return response()->json([
                'contents' => $restrict_content,
                'courses' => $restrict_course
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function sectionConfig($id)
    {
        $config = DB::table('sections')
            ->select('content_title', 'content_author', 'content_subtitle', 'content_description', 'qtd_per_page', 'allow_likes', 'allow_comments')
            ->where('id', $id)
            ->first();

        return $config;
    }

    public function seedContentsComments(Request $request)
    {
        try {

            $content = $this->content->find($request->contents_id);

            $id_comment_sub = ($request->id_comment_sub > 0) ? $request->id_comment_sub : null;

            $approve_comments = $content->author->platform->platformSiteConfig->approve_comments;

            $data = [
                'platform_id' => $request->platform_id,
                'subscriber_id' => auth('api')->user()->id,
                'text' => $request->text,
                'contents_id' => $request->contents_id,
                'id_comment_sub' => $id_comment_sub,
                'approved' => $approve_comments,
            ];

            $comment = $content->comments()->create($data);
            $comment_id = ($request->comment_id > 0) ? $request->comment_id : $comment->id;
            $comment = $comment->update(['comment_id' => $comment_id]);

            $comentario = DB::table('comments')->select('comments.*', 'subscribers.name', 'files.filename')
                ->join('subscribers', 'comments.subscriber_id', '=', 'subscribers.id')
                ->leftJoin('files', 'subscribers.thumb_id', '=', 'files.id')
                ->where('comments.contents_id', $request->contents_id)->orderBy('comments.id', 'desc')->first();


            return response()->json([
                'status' => 'success',
                'data' => $comentario,
                'message' => ''
            ]);

            //return response()->json(['data'=>'hello erro']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }


    }

    public function seedAllComments(Request $request)
    {
        try {
            $content_id = $request->id;
            //UPDATE `comments` SET `id_comment_sub` = NULL WHERE `comments`.`id_comment_sub` = 0

            $comments = $this->comment
                ->with(['subscriber' => function ($query) {
                    $query->with('thumb:id,filename');
                }])
                ->with(['platform_user' => function ($query) {
                    $query->with('thumb:id,filename');
                }])
                ->where('contents_id', $content_id)
                ->where('approved', 1)
                ->orderBy('id', 'desc');

            $Totalcomments = $comments->count();

            if ($Totalcomments > 0) {


                $totalReplies = $this->comment->select('id_comment_sub', DB::raw('COUNT(id_comment_sub) AS qtd'))
                    ->groupBy('id_comment_sub')
                    ->havingRaw('COUNT(id_comment_sub) > 0')
                    ->orderByRaw('COUNT(id_comment_sub) DESC')
                    ->where('approved', 1)
                    ->get();


                $replies = $this->comment
                    ->with(['subscriber' => function ($query) {
                        $query->with('thumb:id,filename');
                    }])
                    ->with(['platform_user' => function ($query) {
                        $query->with('thumb:id,filename');
                    }])
                    ->where('approved', 1)
                    ->where('id_comment_sub', '<>', null)->get();

                $comments = $comments->where('id_comment_sub', null)
                    ->get();

                return response()->json([
                    'comments' => $comments,
                    'replies' => $replies,
                    'totalComments' => $Totalcomments,
                    'totalReplies' => $totalReplies
                ]);

            } else {
                return response()->json([
                    'comments' => 'Sem comentários ainda, seja o primeiro a comentar',
                    'totalComments' => 0
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);

        }

    }

    public function seedAllCommentsReplies(Request $request)
    {

        try {
            $response = "";
            $replies = DB::table('comments')->select('comments.*', 'subscribers.name')
                ->join('subscribers', 'comments.subscriber_id', '=', 'subscribers.id')
                ->where('approved', 1)
                ->where('id_comment_sub', $request->id)->get();
            if ($replies) {
                $response = [];
                foreach ($replies as $reply) {
                    $response[] = $reply;
                }
                return response()->json(['comments' => $response]);

            }

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);

        }


    }

    private function checkContent($platform_id)
    {
        if ($platform_id != auth('api')->user()->platform_id) {
            throw new Exception("Conteúdo não corresponde ao site.");
        }
    }

    public function deleteReplyComment(Request $request)
    {
        try {
            $subscriber_auth = auth('api')->user()->id;
            $reply_id = $request->input('reply_id');
            $subscriber_id = $request->input('subscriber_id');
            $comment_id = $request->input('comment_id');

            if ($subscriber_id == $subscriber_auth) {
                $subscriber_id = $subscriber_auth;
            } else {
                return response()->json([
                    'error' => true,
                    'response' => 'Você não tem permissão para excluir esse comentário.'
                ]);
            }

            if ($comment_id) {
                $this->comment
                    ->where('comment_id', $comment_id)
                    ->delete();
            } else {
                $this->comment
                    ->where('id', $reply_id)
                    ->where('subscriber_id', $subscriber_id)
                    ->where('id_comment_sub', '<>', null)
                    ->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Resposta removida com sucesso.',
                'data' => $request->all(),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }

    }
}
