<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use App\ContentLog;
use Auth;

class AuditRoute
{

    protected $except = [
        'subscribers/get-subscriber-data',
        'content/get-content-data'
    ];

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        $params = Route::current()->parameters();

        $section_id = 0;
        $section_key = "";
        $tags = Route::current()->uri;
        $term = 'section';
        $pattern = '/' . $term . '/';
        if (preg_match($pattern, $tags)) {
            if (isset($params['id'])) $section_id = (int)$params['id'];
            if (isset($params['section_key'])) $section_key = $params['section_key'];
        }

        $content_id = 0;
        $tags = Route::current()->uri;
        $term = 'content';
        $pattern = '/' . $term . '/';
        if (preg_match($pattern, $tags)) {
            if (isset($params['id'])) $content_id = (int)$params['id'];
        }

        $course_id = 0;
        $tags = Route::current()->uri;
        $term = 'course';
        $pattern = '/' . $term . '/';
        if (preg_match($pattern, $tags)) {
            if (isset($params['course_id'])) $course_id = (int)$params['course_id'];
        }

        $newUrl = str_replace('}', '', str_replace('{', '', Route::current()->uri));

        foreach ($params as $key => $value) {
            $newUrl = str_replace($key, $value, $newUrl);
        }

        $response = $next($request);

        $user = Auth::user();

        if ($user === null) {
            $user = auth('api')->user();
            $table = "subscribers";
        } else {
            $table = $user->getTable();
        }

        if ($user === null) {
            return $response;
        }

        // Finaliza o conteúdo
        (new \App\ContentLog)->updateContentLog($user->id);

        $data = [
            'user_id' => $user->id,
            'user_type' => $table,
            'platform_id' => $user->platform_id,
            'ip' => $_SERVER["REMOTE_ADDR"],
            'route' => $newUrl,
            'section_id' => $section_id,
            'section_key' => $section_key,
            'content_id' => $content_id,
            'course_id' => $course_id
        ];

        ContentLog::create($data);

        return $response;
    }
}
