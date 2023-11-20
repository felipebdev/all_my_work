<?php

namespace App\Http\Controllers\Api;

use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\CourseExperience\CourseExperienceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\LA\CacheClearService;

class CourseExperienceController extends Controller
{
    use CustomResponseTrait;

    private CourseExperienceService $courseExperienceService;
    private CacheClearService $cacheClearService;

    public function __construct(CourseExperienceService $courseExperienceService, CacheClearService $cacheClearService)
    {
        $this->courseExperienceService = $courseExperienceService;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * Get modules (all, byID)
     * @param Request $request
     * @return JsonResponse
     */
    public function getModules(Request $request): JsonResponse
    {
        try {
            $courseId = $request->input('course') ?? null;
            $moduleId = $request->input('module') ?? null;

            if ($courseId) {
                $modules = $this->courseExperienceService->getModulesByCourseId($courseId);
                return $this->customJsonResponse('', 200, ['modules' => $modules]);
            }

            if ($moduleId) {
                $module = $this->courseExperienceService->getModuleById($moduleId);
                return $this->customJsonResponse('', 200, ['module' => $module]);
            }

            return $this->customJsonResponse('', 200, ['modules' => []]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get contents (all, byId, withoutCategory)
     * @param Request $request
     * @return JsonResponse
     */
    public function getContents(Request $request): JsonResponse
    {
        try {
            $moduleId = $request->input('module') ?? null;
            $contentId = $request->input('content') ?? null;

            if ($moduleId) {
                $contents = $this->courseExperienceService->getContentsByModuleId($moduleId);
                return $this->customJsonResponse('', 200, ['contents' => $contents]);
            }

            if ($contentId) {
                $content = $this->courseExperienceService->getContentById($contentId);
                return $this->customJsonResponse('', 200, ['content' => $content]);
            }

            if ($request->input('noCategory')) {
                $contents = $this->courseExperienceService->getContentWithoutCategories();
                return $this->customJsonResponse('', 200, ['contents' => $contents]);
            }

            return $this->customJsonResponse('', 200, []);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Delete content by ID
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteContent(Request $request): JsonResponse
    {
        try {
            $contentId = $request->input('content');
            if (!$contentId || !$request->has('content')) throw new Exception('Conteúdo não enviado.');
            $this->courseExperienceService->deleteContentById($contentId);
            $this->cacheClearService->clearContentCache($contentId);
            return $this->customJsonResponse('Item removido com sucesso!', 204);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Add new or update exists flow
     * @param Request $request
     * @return JsonResponse
     */
    public function saveModule(Request $request): JsonResponse
    {
        try {
            $contents = $request->input('content') ?? 0;
            $contentFiles = $request->file('content') ?? null;
            $diagram = $request->input('diagram') ?? null;
            $flowId = $request->input('flowId') ?? null;
            $courseId = $request->input('course') ?? null;
            $flowName = $request->input('flowName') ?? null;

            // Get all the files sent and add to the contents array
            if ($contents != 0 && $contentFiles != null) {
                for ($i = 0; $i < count($contents); $i++) {
                    if (!array_key_exists($i, $contentFiles)) {
                        continue;
                    }
                    $contents[$i]['file'] = $contentFiles[$i]['file'] ?? null;
                }
            }

            // // Flow validations
            if (count($contents) == 0) throw new Exception('Você precisa adicionar ao menos 1 conteúdo.');
            if (!$flowName) throw new Exception('Você precisa nomear o fluxo.');
            if (count($contents) == 0 && $diagram != null) throw new Exception('Fluxo inválido.');

            // // Create or update flow
            if ($flowId) {
                $this->courseExperienceService->updateFlow($flowId, $courseId, $flowName, $diagram, $contents);
                return $this->customJsonResponse('Fluxo atualizado com sucesso.', 200, []);
            } else {
                $this->courseExperienceService->addFlow($courseId, $flowName, $diagram, $contents);
                return $this->customJsonResponse('Fluxo criado com sucesso.', 201, []);
            }
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get all authors added in this platform
     * @return JsonResponse
     */
    public function getAuthors(): JsonResponse
    {
        try {
            $authors = $this->courseExperienceService->getAuthors();
            return $this->customJsonResponse('', 200, ['authors' => $authors]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Create new author
     * @param Request $request
     * @return JsonResponse
     */
    public function createAuthor(Request $request): JsonResponse
    {
        try {
            $hasAuthor = $this->courseExperienceService->authorExists($request->input('email'));
            if ($hasAuthor) throw new Exception('Email já cadastrado');

            $author = $this->courseExperienceService->addAuthor($request->name, $request->email, $request->desc);

            return $this->customJsonResponse('Autor criado com sucesso!', 200, ['author' => $author]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function syncDiagram(Request $request)
    {
        try {
            $module = $this->courseExperienceService->getModuleById($request->input('moduleId'));
            $contents = $this->courseExperienceService->getContentsByModuleId($module->id);

            $diagramNode = '';
            $linkArray = '';
            $x = -158;
            $y = -372;

            $indexController = 0;

            $firstLink = 0;
            $lastLink = 0;

            foreach ($contents as $content) {
                $content->category = 'content';
                $content->content_id = $content->id;
                $content->description = $content->description ?? $content->title;
                $content->save();

                $linkArray .= '{
                    "from": ' . ($firstLink == 0 ? '-1' : $lastLink) . ',
                    "to": ' . (isset($contents[$indexController]) ? $contents[$indexController]->id : '-2') . '
                  },';

                $firstLink = (isset($contents[$indexController]) ? $contents[$indexController]->id : '-2');
                $lastLink = (isset($contents[$indexController]) ? $contents[$indexController]->id : '-2');

                $indexController++;

                $diagramNode .= '{
                    "key": ' . $content->id . ',
                    "loc": "' . $x . ' ' . $y . '",
                    "text": "Conteúdo",
                    "icon": "file.png",
                    "category": "content",
                    "title": "' . $content->title . '",
                    "id": ' . $content->id . '
                  }' . (isset($contents[$indexController]) ? ',' : '');

                $x = $x + 80;
                $y = $y + 120;
            }


            $diagram = '{
                "class": "GraphLinksModel",
                "nodeDataArray": [
                  {
                    "key": -1,
                    "loc": "-231 -331",
                    "text": "INÍCIO",
                    "category": "start"
                  },
                  {
                    "key": -2,
                    "loc": "659 85",
                    "text": "FIM",
                    "category": "end"
                  },
                  ' . $diagramNode . '
                ],
                "linkDataArray": [
                  ' . $linkArray . '
                  {
                    "from": ' . $lastLink . ',
                    "to": -2
                  }
                ]
              }';

            $module->diagram = trim(preg_replace('/\s\s+/', ' ', $diagram));
            $module->save();

            return $this->customJsonResponse('Módulo sincronizado com sucesso!', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Experience view route
     * @param mixed $course_id
     * @return View|Factory|RedirectResponse
     * @throws BindingResolutionException
     * @throws RouteNotFoundException
     */
    public function experience($course_id)
    {
        $data = [
            "course" => Course::findOrFail($course_id),
            "course_id" => $course_id,
            "gjs" => env('XGROW_EXPERIENCE')
        ];

        return view('course.lessons.index', $data);
    }
}
