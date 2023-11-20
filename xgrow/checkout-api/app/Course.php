<?php

namespace App;

use App\Alert;
use App\Author;
use App\Category;
use App\Certificate;
use App\Content;
use App\CourseSubscriber;
use App\Module;
use App\PaymentCards;
use App\Plan;
use App\Platform;
use App\Subscriber;
use App\Template;
use App\Warning;
use App\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{

    const STATUS_IN_PROGRESS = "Em andamento";
    const STATUS_NOT_STARTED = "Não iniciado";
    const STATUS_CONCLUDED = "Concluído";

    const TEMPLATE_DEFAULT_MODEL = 2;

    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($course) {
            $items['item_type'] = 2;
            $items['platform_id'] = $course->platform_id;
            $items['item_id'] = $course->id;
            Menu::deleteItem($items);

            $items['model_type'] = 2;
            $items['model_id'] = $course->id;
            Widget::deleteItem($items);
        });

        static::created(function ($course) {     
            $items['item_type'] = 2;
            $items['item_id'] = $course->id;
            $items['visible'] = $course->active;
            $items['platform_id'] =  $course->platform_id;
            Menu::createItem($items);
        });

        static::updated(function ($course) {     
            $items['item_type'] = 2;
            $items['item_id'] = $course->id;
            $items['visible'] = $course->active;
            $items['platform_id'] =  $course->platform_id;
            Menu::visibilityItem($items);
        });     

    }

    protected $fillable = [
        'name', 'description', 'total_hours', 'active', 'has_limit', 'vacancies', 'thumb_id', 'icon_id', 'platform_id', 'author_id', 'paid', 'plan_id', 'template_id', 'started_at', 'form_delivery', 'delivery_model', 'delivery_date', 'delivered_at', 'frequency',
        'restrict_date', 'restrict_plan', 'restrict_start', 'restrict_finish'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function thumb()
    {
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function icon()
    {
        return $this->hasOne(File::class, 'id', 'icon_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function subscribers()
    {
        return $this->hasMany(CourseSubscriber::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentCards::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'course_plan', 'course_id', 'plan_id');
    }

    public function checkCertificate($subscriber_id, $course_id)
    {

        $data['certificate'] = 0;
        $data['token'] = null;
        $data['certificated_at'] = null;

        $data['total_class'] = Content::where('course_id', $course_id)
            ->where('module_id', '>', 0)
            ->where('published', 1)
            ->where('is_course', 1)->count();

        $data['total_classes_attended'] = $this->updateTotalClassesAttended($course_id, $subscriber_id);

        $certificate_active = $this->find($course_id)->certificate->active;

        if ($certificate_active == 1 and $data['total_classes_attended'] >= $data['total_class']) {
            $data['certificate'] = 1;
            $data['token'] = generateRandomString();
            $data['certificated_at'] = date('Y-m-d H:i:s');
        }

        CourseSubscriber::updateOrCreate(
            [
                'course_id' => $course_id,
                'subscriber_id' => $subscriber_id,
            ], $data
        );

        return $data;
    }


    public function updateTotalClassesAttended($course_id, $subscriber_id)
    {
        $result = DB::select("SELECT COUNT(*) count FROM courses a INNER JOIN contents b INNER JOIN content_subscriber c ON a.id = b.course_id AND b.id = c.content_id WHERE c.subscriber_id = :subscriber_id AND a.id = :course_id AND c.concluded = '1' AND b.published = 1", [
            ':course_id' => $course_id,
            ':subscriber_id' => $subscriber_id,
        ]);
        return $result[0]->count;
    }

    public function getNextContent($content_id)
    {
        $content = Content::find($content_id);
        $result = DB::select("SELECT c.order, a.id, a.module_id, a.featured_order, b.name, a.title FROM contents a INNER JOIN courses b INNER JOIN modules c  ON a.course_id = b.id AND b.id = c.course_id AND a.module_id = c.id WHERE ((c.order = :module_order AND a.featured_order > :featured_order) OR c.order > :module_order_2) AND b.id = :course_id AND a.published = 1 ORDER BY c.order ASC, a.featured_order ASC LIMIT 1", [
            ':course_id' => $content->course->id,
            ':featured_order' => $content->featured_order,
            ':module_order' => $content->module->order,
            ':module_order_2' => $content->module->order, //You cannot use the same parameter name more than once, so try something like this
        ]);

        if (isset($result[0])) {
            $next_content = $result[0];
        } else {
            $next_content = $this->getFirstClass($content->course->id);
        }

        return $next_content;
    }

    public function getFirstClass($course_id)
    {
        $result = DB::select("SELECT c.order, a.module_id, a.featured_order, a.id, b.name, a.title FROM contents a INNER JOIN courses b INNER JOIN modules c  ON a.course_id = b.id AND b.id = c.course_id AND a.module_id = c.id WHERE b.id = :course_id AND a.published = 1 ORDER BY c.order ASC, a.featured_order ASC LIMIT 1", [
            ':course_id' => $course_id
        ]);

        if (!isset($result[0])) {
            return [];
        }

        return $result[0];
    }

    public function getStartContent($course_id, $subscriber_id)
    {

        $result = DB::select("SELECT a.name, b.title, b.id, b.module_id, c.updated_at FROM courses a INNER JOIN contents b INNER JOIN content_subscriber c ON a.id = b.course_id AND b.id = c.content_id AND c.subscriber_id = :subscriber_id WHERE a.id = :course_id AND b.published = 1  ORDER BY c.updated_at DESC LIMIT 1", [
            ':course_id' => $course_id,
            ':subscriber_id' => $subscriber_id,
        ]);

        if (isset($result[0])) {
            $next_content = $result[0];
        } else {
            $next_content = $this->getFirstClass($course_id);
        }

        return $next_content;
    }

    static function havePermission($course_id, $subscriber_id)
    {
        $plans = Subscriber::getPlans($subscriber_id);

        $course = self::find($course_id);

        return ($course->plans()->whereIn('plan_id', $plans)->count() > 0 or Subscriber::checkIfSubscriberHasUnlimitedPlans($subscriber_id));
    }


    static function notAllowed($course_id, $subscriber_id)
    {
        return !self::havePermission($course_id, $subscriber_id);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function orderable()
    {
        return $this->morphMany('App\Order', 'orderable');
    }

    public function getMostViewedCourses($platform_id)
    {
        $sql = "SELECT b.name, b.author_id, SUM(c.views) as views FROM modules a
        INNER JOIN courses b ON b.id = a.course_id
        INNER JOIN contents c ON c.id = a.id
        WHERE b.platform_id = '" . $platform_id . "'
        AND b.active = 1
        GROUP BY  b.id";

        return $sql;
    }

    public function mostViewedCourseByDayWeek($platform_id, $course_id)
    {
        $sql = "SELECT b.name, DAYOFWEEK(d.created_at) AS day, COUNT(*) AS amount FROM modules a
        INNER JOIN courses b ON b.id = a.course_id
        INNER JOIN contents c ON c.id = a.id
        INNER JOIN content_logs d ON d.content_id = c.id
        WHERE b.platform_id = '$platform_id'
        AND d.user_type = 'subscribers'
        AND b.id = $course_id
        GROUP BY DAYOFWEEK(d.created_at)
        ORDER BY day";

        return $sql;
    }

    public function getCourseByPlatformId($plataform_id)
    {
        $sql = "SELECT id, name FROM courses
        WHERE platform_id = '$plataform_id'
        AND active = 1
        ORDER BY id";

        return $sql;
    }

    public function getSubscriberCourse($platform_id, $status)
    {
        $sql = "SELECT a.name, a.email, c.name AS course, a.status, a.last_acess, a.created_at FROM subscribers a
                LEFT JOIN course_subscribers b ON a.id = b.subscriber_id
                LEFT JOIN courses c ON b.course_id = c.id
                WHERE a.platform_id = '$platform_id' ";
        if ($status === 'sem_curso') {
            $sql .= "AND b.course_id IS NULL ";
        }
        if ($status === 'com_curso') {
            $sql .= "AND b.course_id IS NOT NULL ";
        }
        $sql .= "AND a.status = 'active' ORDER BY a.name";

        return $sql;
    }

    public function getDefaultTemplate(){
        $template = new Template();

        $template_default = $template->where('course_model', self::TEMPLATE_DEFAULT_MODEL)->first();

        if($template_default){
            $template_id = $template_default->id;
        }
        else{
            $template_first = $template->where('course', 1)->first();
            $template_id = ($template_first) ? $template_first->id : 0;
        }

        return $template_id;

    }
}
