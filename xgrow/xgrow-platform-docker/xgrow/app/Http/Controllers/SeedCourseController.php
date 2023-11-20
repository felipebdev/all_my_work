<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Content;
use App\ContentLog;
use App\ContentSubscriber;
use App\Course;
use App\CourseSubscriber;
use App\Events\UserAccessedCourse;
use App\Module;
use App\Platform;
use App\NotesSubscribers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use phpDocumentor\Reflection\Types\This;

class SeedCourseController extends Controller
{
    private $course;
    private $platform;
    private $content;
    private $module;
    private $alert;
    private $course_subscribers;
    private $content_subscriber;
    private $notes_subscribers;
    private $content_logs;

    public function __construct(course $course, Platform $platform, Content $content, Module $module, Alert $alert, CourseSubscriber $course_subscribers, ContentSubscriber $content_subscriber, NotesSubscribers $notes_subscribers, ContentLog  $content_logs)
    {
        $this->course = $course;
        $this->platform = $platform;
        $this->content = $content;
        $this->module = $module;
        $this->alert = $alert;
        $this->course_subscribers = $course_subscribers;
        $this->content_subscriber = $content_subscriber;
        $this->notes_subscribers = $notes_subscribers;
        $this->content_logs = $content_logs;
    }

    //pega todos os cursos da plataforma
    public function seedCourses(Request $request)
    {
        try {
            $platform = $this->platform->find($request->platform_id);
            if ($platform) {

                
                $courses = $this->course
                    ->where('platform_id', $request->platform_id)
                    ->where('active', 1)
                    ->with('thumb:id,filename')
                    ->with('template:id,course_model')
                    ->get();

                
                $restrict_course = [];
                foreach ($courses as $key => $course) {
                    if($this->course->havePermission($course->id, auth('api')->user()->id)){
                        $restrict_course[] = $courses[$key];
                    }
                }
           

                return response()->json([
                    'courses' => $restrict_course
                ], 200, array(), JSON_PRETTY_PRINT);
            }
            return response()->json(['error' => true, 'message' => 'Course not found']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }

    public function seedGetStartContent(Request $request)
    {
        try {

            $class = $this->course->getStartContent($request->course_id, auth('api')->user()->id);

            return response()->json($class);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);

        }
    }


    public function seedClassById(Request $request)
    {
        try {

            if ($request->id > 0) {
                $content_id = $request->id;
            } else {
                $class = $this->course->getStartContent($request->course_id, auth('api')->user()->id);
                $content_id = $class->id;
            }

            if(Content::havePermission($content_id, auth('api')->user()->id)){
                $subscriber_id = auth('api')->user()->id;

                $published = $this->module->checkIfPublished($content_id, $subscriber_id);

                
                if ($published['visibled'] == '0') {
                    throw new Exception("Aula não publicada.");
                }


                $content = $this->content
                    ->with('thumb_small:id,filename')
                    ->with('thumb_big:id,filename')
                    ->with('course')
                    ->with('attachs')
                    ->with('author:id,name_author,author_photo,author_insta,author_linkedin,author_youtube,author_desc')
                    ->find($content_id);

                //checa se conteúdo correponde a plataforma
                $this->checkContent($content->course->platform_id);

                //próxima aula
                $next_content = $this->course->getNextContent($content->id);

                if ($content) {
                    $now = date('Y-m-d H:i:s');
                    $alerts = $this->alert
                        ->where('course_id', $content->course_id)
                        ->where('expires_in', '>', $now)->get();


                    foreach ($alerts as $index => $alert) {
                        $views = $alert->subscribers()
                            ->where('subscriber_id', $subscriber_id)
                            ->count();

                        if ($views == 0) {
                            $alert->subscribers()->attach($subscriber_id);
                        }
                        $alerts[$index]['views'] = $views;
                    }


                    $content_subscribers = $this->content_subscriber->where('content_id', $content->id)
                        ->where('subscriber_id', $subscriber_id);
                    if ($content_subscribers->count() == 0) {
                        $content->subscribers()->attach($subscriber_id);
                    } else {
                        $content_subscribers->update(['updated_at' => now()]);
                    }

                    $data = $this->course->checkCertificate($subscriber_id, $content->course_id);

                    $note = $this->notes_subscribers->where('subscriber_id', $subscriber_id)
                        ->where('content_id', $content_id)
                        ->first();

                    UserAccessedCourse::dispatch(auth('api')->user(), $this->course->find($content->course_id));

                    return response()->json([
                        'content' => $content,
                        'next_content' => $next_content,
                        'alerts' => $alerts,
                        'now' => $now,
                        'certificate' => $data['certificate'],
                        'token' => $data['token'],
                        'note' => $note,
                    ]);


                }
                return response()->json(['error' => true, 'message' => 'Content not found']);
            }
            else{
                return response()->json(['status' => 'Unauthorized user'], 401);
            }
  

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }


    public function seedModulesAndClasses(Request $request)
    {
        try {

             //checa se curso correponde a plataforma
            $this->checkContent($this->course->find($request->course_id)->platform_id);

            $modules = $this->module->classes($request->course_id, auth('api')->user()->id);

            if ($modules) {
                return response()->json([
                    'modules' => $modules
                ], 200, array(), JSON_PRETTY_PRINT);
            }
            return response()->json(['error' => true, 'message' => 'Modules not found']);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function seedSetClassWatched(Request $request)
    {
        try {
            $content = $this->content->find($request->content_id);

            if ($content) {
                $subscriber_id = auth('api')->user()->id;

                $concluded = ($request->watched === 'true') ? 1 : 0;

                $this->content_subscriber->updateOrCreate(
                    [
                        'content_id' => $content->id,
                        'subscriber_id' => $subscriber_id,
                    ],
                    [
                        'content_id' => $content->id,
                        'subscriber_id' => $subscriber_id,
                        'concluded' => $concluded,
                    ]
                );

                $data = $this->course->checkCertificate($subscriber_id, $content->course_id);

                return response()->json([
                    'content' => $content,
                    'token' => $data['token'],
                    'total_classes_attended' => $data['total_classes_attended'],
                    'total_class' => $data['total_class'],
                    'certificate' => $data['certificate']
                ]);
            }

            return response()->json(['error' => true, 'message' => 'Content not found']);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getCourseConfig(Request $request)
    {
        try {

            $course = $this->course
                ->with('template:id,course_model')
                ->find($request->id);

            $class = $this->course->getStartContent($request->id, auth('api')->user()->id);

            return response()->json([
                'course' => $course,
                'class' => $class,
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function saveNote(Request $request)
    {
        try {

            $this->notes_subscribers->updateOrCreate(
                [
                    'content_id' => $request->content_id,
                    'subscriber_id' => auth('api')->user()->id
                ],
                [
                    'note' => $request->note
                ]
            );

            return response()->json(['status' => 'success', 'message' => 'Saved note']);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }

    }

    public function getVerifyUrl(Request $request)
    {

        try {

            $course = $this->course->where('id', $request->course_id)->latest()->first();

            $platform_id = $request->platform_id;
            $plan_id = base64_encode($course->plan_id);
            $course_id = base64_encode($request->course_id);
            $subscriber_id = base64_encode(auth('api')->user()->id);
            $url = config('app.url') . "/getnet/$platform_id/$plan_id/$subscriber_id/c/$course_id";

            if ($course->paid == 1) {

                $status = DB::table('payment_cards')->select('status')->where([
                    ['course_id', '=', $request->course_id],
                    ['subscriber_id', '=', auth('api')->user()->id]
                ])->latest()->first();

                if ($status) {

                    if ($status->status === 'APPROVED') {
                        return response()->json([
                            'status' => 1

                        ]);
                    } else {
                        return response()->json([
                            'status' => 0,
                            'url' => $url
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'url' => $url
                    ]);
                }


            } else {
                return response()->json([
                    'status' => 1
                ]);
            }

        } catch (Exception $e) {
            return response()->json(['response' => $e->getMessage()]);
        }

    }


    private function checkContent($platform_id)
    {
        if ($platform_id != auth('api')->user()->platform_id) {
            throw new Exception("Conteúdo não corresponde ao site.");
        }
    }

    public function saveAccessCourseLog(Request $request)
    {
        try {
            $this->content_logs::create($request->all());
            return response()->json([
                'data' => $request->all()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
