<?php

namespace App\Services\LA;

use DomainException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CacheClearService
{
    private LaCacheBaseService $laCacheBaseService;

    /**
     * @param LaCacheBaseService $laCacheBaseService
     * @return void
     */
    public function __construct(LaCacheBaseService $laCacheBaseService)
    {
        $this->laCacheBaseService = $laCacheBaseService;
    }

    /** Clear content cache on LA
     * @param mixed $contentId
     * @return void
     * @throws DomainException
     * @throws GuzzleException
     */
    public function clearContentCache($contentId)
    {
        try {
            $req = $this->laCacheBaseService->connectionConfig(Auth::user()->platform_id);
            $post = $req->post('platform-authorized/clean-cache/content/prerendering', ['json' => ['contentId' => $contentId]]);
            Log::info('Clear Content Cache', [
                'message' => $post->getBody()->getContents(),
                'uri' => 'platform-authorized/clean-cache/content/prerendering',
                'contentId' => $contentId,
                'platformId' => Auth::user()->platform_id,
                'code' => $post->getStatusCode()
            ]);
        } catch (Exception $e) {
            Log::error('Clear Content Cache', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /** Clear subscriber cache on LA
     * @param mixed $platformId
     * @param mixed $email
     * @param mixed $subscriberId
     * @throws Exception
     * @throws GuzzleException
     */
    public function clearSubscriberCache($platformId, $email = null, $subscriberId = null)
    {
        try {
            $req = $this->laCacheBaseService->connectionConfig($platformId);
            if ($email) {
                $post = $req->post('platform-authorized/clean-cache/subscriber/login', ['json' => ['subscriberEmail' => $email]]);
                Log::info('Clear Subscriber Cache :: Email', [
                    'message' => $post->getBody()->getContents(),
                    'uri' => 'platform-authorized/clean-cache/subscriber/login',
                    'email' => $email,
                    'platformId' => $platformId,
                    'code' => $post->getStatusCode()
                ]);
            }

            if ($subscriberId) {
                $post = $req->post('platform-authorized/clean-cache/subscriber/userinfo', ['json' => ['subscriberId' => $subscriberId]]);
                Log::info('Clear Subscriber Cache :: SubscriberID', [
                    'message' => $post->getBody()->getContents(),
                    'uri' => 'platform-authorized/clean-cache/subscriber/userinfo',
                    'subscriberId' => $subscriberId,
                    'platformId' => $platformId,
                    'code' => $post->getStatusCode()
                ]);
            }

            if ($subscriberId) {
                $post = $req->post('platform-authorized/clean-cache/subscriber/content', ['json' => ['subscriberId' => $subscriberId]]);
                Log::info('Clear Subscriber Cache :: SubscriberID', [
                    'message' => $post->getBody()->getContents(),
                    'uri' => 'platform-authorized/clean-cache/subscriber/content',
                    'subscriberId' => $subscriberId,
                    'platformId' => $platformId,
                    'code' => $post->getStatusCode()
                ]);
            }
        } catch (Exception $e) {
            Log::error('Clear Subscriber Cache', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    public function clearSubscriberContentCache($platformId, $subscriberId)
    {
        $req = $this->laCacheBaseService->connectionConfig($platformId);

        try {
            $post = $req->post('platform-authorized/clean-cache/subscriber/content', ['json' => ['subscriberId' => $subscriberId]]);
            Log::info('Clear Subscriber Cache :: SubscriberID', [
                'message' => $post->getBody()->getContents(),
                'uri' => 'platform-authorized/clean-cache/subscriber/content',
                'subscriberId' => $subscriberId,
                'platformId' => $platformId,
                'code' => $post->getStatusCode()
            ]);
        } catch (Exception $e) {
            Log::error('Clear Subscriber Cache', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /** Clear course cache on LA
     * @param mixed $courseId
     * @param mixed $classId
     * @return void
     * @throws DomainException
     * @throws GuzzleException
     */
    public function clearCourseCache($courseId = null, $classId = null)
    {
        try {
            $req = $this->laCacheBaseService->connectionConfig(Auth::user()->platform_id);

            if ($courseId) {
                $post = $req->post('platform-authorized/clean-cache/course/module-and-classes', ['json' => ['courseId' => $courseId]]);
                Log::info('Clear Course Cache :: SubscriberID', [
                    'message' => $post->getBody()->getContents(),
                    'uri' => 'platform-authorized/clean-cache/course/module-and-classes',
                    '$courseId' => $courseId,
                    'platformId' => Auth::user()->platform_id,
                    'code' => $post->getStatusCode()
                ]);
            }

            if ($classId) {
                $post = $req->post('platform-authorized/clean-cache/course/seed-class-by-id', ['json' => ['courseId' => $courseId, 'classId' => $classId]]);
                Log::info('Clear Course Cache :: SubscriberID', [
                    'message' => $post->getBody()->getContents(),
                    'uri' => 'platform-authorized/clean-cache/course/seed-class-by-id',
                    '$classId' => $classId,
                    'platformId' => Auth::user()->platform_id,
                    'code' => $post->getStatusCode()
                ]);
            }
            $req->post('platform-authorized/clean-cache/course/prerendering');
        } catch (Exception $e) {
            Log::error('Clear Course Cache', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /** Clear Section Cache
     * @return void
     * @throws DomainException
     * @throws GuzzleException
     */
    public function clearSectionCache()
    {
        try {
            $req = $this->laCacheBaseService->connectionConfig(Auth::user()->platform_id);
            $post = $req->post('platform-authorized/clean-cache/section/prerendering');
            Log::info('Clear Section Cache', [
                'message' => $post->getBody()->getContents(),
                'uri' => 'platform-authorized/clean-cache/section/prerendering',
                'platformId' => Auth::user()->platform_id,
                'code' => $post->getStatusCode()
            ]);
        } catch (Exception $e) {
            Log::error('Clear Section Cache', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /** Clear All Subscribers Cache
     * @return void
     * @throws DomainException
     * @throws GuzzleException
     */
    public function clearAllCachesFromSubscribers()
    {
        try {
            $req = $this->laCacheBaseService->connectionConfig(Auth::user()->platform_id);
            $post = $req->post('platform-authorized/clean-cache/subscriber/all');
            Log::info('Clear Section Cache', [
                'message' => $post->getBody()->getContents(),
                'uri' => 'platform-authorized/clean-cache/subscriber/all',
                'platformId' => Auth::user()->platform_id,
                'code' => $post->getStatusCode()
            ]);
        } catch (Exception $e) {
            Log::error('Clear All Caches from Subscribers', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }
}
