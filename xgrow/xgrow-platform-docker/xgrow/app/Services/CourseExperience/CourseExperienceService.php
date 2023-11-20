<?php

namespace App\Services\CourseExperience;

use App\Author;
use App\Content;
use App\Course;
use App\Helpers\SecurityHelper;
use App\Module;
use App\Services\Storage\UploadedImage;
use App\Services\Storage\UploadFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\LA\CacheClearService;

class CourseExperienceService
{
    const IMAGE_TYPES = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
    private CacheClearService $cacheClearService;

    public function __construct(CacheClearService $cacheClearService)
    {
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * Get all Modules by course ID
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getModulesByCourseId($id)
    {
        (new SecurityHelper)->securityUser(Course::find($id));
        return Module::select(['id', 'name', 'order', 'diagram'])
            ->where('course_id', $id)
            ->with('contents:id,title,has_audio_link,has_video_link,has_external_link,module_id')
            ->get();
    }

    /**
     * Get Module by ID
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getModuleById($id)
    {
        $module = Module::select(['id', 'name', 'order', 'diagram', 'course_id'])->find($id);
        (new SecurityHelper)->securityUser(Course::find($module->course_id));
        return $module;
    }

    /**
     * Get all Contents by Module ID
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getContentsByModuleId($id)
    {
        return Content::select(['id', 'title', 'has_audio_link', 'has_video_link', 'has_external_link', 'category'])
            ->where('published', 1)
            ->where('module_id', $id)
            ->orderBy('order_course')
            ->get();
    }

    /**
     * Get Content by ID
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getContentById($id)
    {
        $content = Content::select([
            'id', 'title', 'has_audio_link', 'has_video_link', 'has_external_link',
            'audio_link', 'audio_id', 'author_id', 'category', 'content_html', 'external_link',
            'title', 'url', 'video_link', 'description', 'content_id', 'module_id', 'file_url'
        ])->find($id);
        (new SecurityHelper)->securityUser(Author::find($content->author_id));
        return $content;
    }

    /**
     * Get all Contents without Xgrow Experience category
     * @return mixed
     * @throws Exception
     */
    public function getContentWithoutCategories()
    {
        return Content::select(['contents.id', 'contents.title', 'contents.author_id as author_id'])
            ->leftJoin('authors', 'authors.id', '=', 'contents.author_id')
            // ->whereNull('contents.category')
            ->where('contents.published', 1)
            ->where('authors.platform_id', Auth::user()->platform_id)
            ->get();
    }

    /**
     * Delete content by ID
     * @param $id
     * @throws Exception
     */
    public function deleteContentById($id)
    {
        $content = Content::find($id);
        (new SecurityHelper)->securityUser(Author::find($content->author_id));
        $content->delete();
    }

    /**
     * Create new flow (all fields are required)
     * @param $courseId
     * @param $flowName
     * @param $diagram
     * @param $contents
     * @throws Exception
     */
    public function addFlow($courseId, $flowName, $diagram, $contents)
    {
        (new SecurityHelper)->securityUser(Course::find($courseId));
        $moduleOrder = Module::select(['id'])->where('course_id', $courseId)->count();
        $module = new Module;
        $module->name = $flowName;
        $module->enable_comments = true;
        $module->allow_to_order_sequence = true;
        $module->course_id = $courseId;
        $module->order = $moduleOrder + 1;
        $module->diagram = $diagram;
        $module->save();


        $contentOrder = 0;
        foreach ($contents as $content) {
            $modelContent = new Content;
            $modelContent->title = $content['title'];
            $modelContent->description = $content['description'] ?? null;
            $modelContent->author_id = $content['authorId'];
            $modelContent->published = true;
            $modelContent->published_at = Carbon::now();
            $modelContent->course_id = $courseId;
            $modelContent->module_id = $module->id;
            $modelContent->is_course = false;
            $modelContent->category = $content['category'];
            $modelContent->order_course = $contentOrder;
            $contentOrder++;

            if ($content['category'] == 'link') {
                $modelContent->external_link = $content['link'] ?? null;
                $modelContent->has_external_link = true;
            }

            if ($content['category'] == 'video') {
                $modelContent->video_link = $content['link'] ?? null;
                $modelContent->has_video_link = true;
            }

            if ($content['category'] == 'audio') {
                $modelContent->audio_link = $content['link'] ?? null;
                $modelContent->has_audio_link = true;
            }

            if ($content['category'] == 'content') {
                $modelContent->content_id = $content['contentId'] ?? null;
            }

            if ($content['category'] == 'text') {
                $modelContent->content_html = $content['text'] ?? null;
            }

            if ($content['category'] == 'archive') {
                if ($content['file'] !== null) {
                    if (in_array($content['file']->getMimeType(), self::IMAGE_TYPES)) {
                        $uploadImage = new UploadedImage(Auth::user()->platformId, $content['file'], Storage::disk('images'));
                        $uploadedImage = $uploadImage->store();
                        $modelContent->file_url = $uploadedImage->converted;
                    } else {
                        $uploadFile = new UploadFile(Auth::user()->platformId, $content['file'], Storage::disk('images'));
                        $uploadedFileUrl = $uploadFile->store();
                        $modelContent->file_url = $uploadedFileUrl;
                    }
                }
            }

            $modelContent->save();

            $json = json_decode($module->diagram);
            foreach ($json->nodeDataArray as $node) {
                if ($node->key == $content['key']) $node->id = $modelContent->id;
            }

            $module->diagram = json_encode($json);
            $module->save();
        }
    }

    /**
     * Update existent flow
     * @param $id
     * @param $courseId
     * @param $flowName
     * @param $diagram
     * @param $contents
     * @throws Exception
     */
    public function updateFlow($id, $courseId, $flowName, $diagram, $contents)
    {
        (new SecurityHelper)->securityUser(Course::find($courseId));
        $module = Module::find($id);
        $module->name = $flowName;
        $module->course_id = $courseId;
        $module->diagram = $diagram;
        $module->save();

        $json = json_decode($module->diagram);

        $contentOrder = 0;
        foreach ($json->nodeDataArray as $node) {
            /** If item in flow exists (update item) */
            if (isset($node->id) && $node->id != '') {
                $modelContent = Content::find($node->id);
                $modelContent->order_course = $contentOrder;

                $findContent = null;
                foreach ($contents as $content) {
                    if ($content['id'] == $node->id) $findContent = $content;
                    $this->cacheClearService->clearContentCache($node->id);
                    $this->cacheClearService->clearCourseCache($module->course_id, $node->id);
                    $this->cacheClearService->clearCourseCache($module->course_id);
                }

                $modelContent->title = $findContent['title'];
                $modelContent->description = $findContent['description'] ?? null;
                $modelContent->author_id = $findContent['authorId'];

                if ($modelContent->category == 'link') {
                    $modelContent->external_link = $findContent['link'] ?? null;
                    $modelContent->has_external_link = true;
                }

                if ($modelContent->category == 'video') {
                    $modelContent->video_link = $findContent['link'] ?? null;
                    $modelContent->has_video_link = true;
                }

                if ($modelContent->category == 'audio') {
                    $modelContent->audio_link = $findContent['link'] ?? null;
                    $modelContent->has_audio_link = true;
                }

                if ($modelContent->category == 'content') {
                    $modelContent->content_id = $findContent['contentId'] ?? null;
                }

                if ($modelContent->category == 'text') {
                    $modelContent->content_html = $findContent['text'] ?? null;
                }

                if ($modelContent->category == 'archive') {
                    if ($findContent['file'] !== null) {
                        if (in_array($findContent['file']->getMimeType(), self::IMAGE_TYPES)) {
                            $uploadImage = new UploadedImage(Auth::user()->platformId, $findContent['file'], Storage::disk('images'));
                            $uploadedImage = $uploadImage->store();
                            $modelContent->file_url = $uploadedImage->converted;
                        } else {
                            $uploadFile = new UploadFile(Auth::user()->platformId, $findContent['file'], Storage::disk('images'));
                            $uploadedFileUrl = $uploadFile->store();
                            $modelContent->file_url = $uploadedFileUrl;
                        }
                    }
                }

                $modelContent->save();
            }

            /** If item doesn't exists in flow (create item) */
            if (isset($node->id) && $node->id == '') {
                $findContent = null;
                foreach ($contents as $content) {
                    if ($content['key'] == $node->key) $findContent = $content;
                }

                $newContent = new Content;
                $newContent->title = $findContent['title'];
                $newContent->description = $findContent['description'] ?? null;
                $newContent->author_id = $findContent['authorId'];
                $newContent->published = true;
                $newContent->published_at = Carbon::now();
                $newContent->course_id = $courseId;
                $newContent->module_id = $module->id;
                $newContent->is_course = false;
                $newContent->category = $findContent['category'];
                $modelContent->order_course = $contentOrder;


                if ($findContent['category'] == 'link') {
                    $newContent->external_link = $findContent['link'] ?? null;
                    $newContent->has_external_link = true;
                }

                if ($findContent['category'] == 'video') {
                    $newContent->video_link = $findContent['link'] ?? null;
                    $newContent->has_video_link = true;
                }

                if ($findContent['category'] == 'audio') {
                    $newContent->audio_link = $findContent['link'] ?? null;
                    $newContent->has_audio_link = true;
                }

                if ($findContent['category'] == 'content') {
                    $newContent->content_id = $findContent['contentId'] ?? null;
                }

                if ($findContent['category'] == 'text') {
                    $newContent->content_html = $findContent['text'] ?? null;
                }

                if ($findContent['category'] == 'archive') {
                    if ($findContent['file'] !== null) {
                        if (in_array($findContent['file']->getMimeType(), self::IMAGE_TYPES)) {
                            $uploadImage = new UploadedImage(Auth::user()->platformId, $findContent['file'], Storage::disk('images'));
                            $uploadedImage = $uploadImage->store();
                            $newContent->file_url = $uploadedImage->converted;
                        } else {
                            $uploadFile = new UploadFile(Auth::user()->platformId, $findContent['file'], Storage::disk('images'));
                            $uploadedFileUrl = $uploadFile->store();
                            $newContent->file_url = $uploadedFileUrl;
                        }
                    }
                }

                $newContent->save();

                foreach ($json->nodeDataArray as $localNode) {
                    if ($localNode->key == $findContent['key']) {
                        $localNode->id = $newContent->id;
                        $this->cacheClearService->clearContentCache($newContent->id);
                        $this->cacheClearService->clearCourseCache($module->course_id, $newContent->id);
                        $this->cacheClearService->clearCourseCache($module->course_id);
                    }
                }

                $module->diagram = json_encode($json);
                $module->save();
            }

            $contentOrder++;
        }
    }

    /**
     * Get all authors by platformId
     * @return mixed
     */
    public function getAuthors()
    {
        return Author::select(['id', 'name_author AS name'])
            ->where('status', 1)
            ->where('platform_id', Auth::user()->platform_id)
            ->get();
    }

    /**
     * Add new author
     * @param $name
     * @param $email
     * @param $description
     * @return mixed
     */
    public function addAuthor($name, $email, $description)
    {
        return Author::create([
            'name_author' => $name,
            'author_email' => $email,
            'author_desc' => $description,
            'platform_id' => Auth::user()->platform_id
        ]);
    }

    /**
     * Verify if the author exists in this platform
     * @param $email
     * @return mixed
     */
    public function authorExists($email)
    {
        return Author::where('author_email', '=', $email)
            ->where('platform_id', '=', Auth::user()->platform_id)
            ->exists();
    }
}
