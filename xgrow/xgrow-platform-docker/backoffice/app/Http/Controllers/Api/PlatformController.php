<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Helpers\CollectionHelper;
use App\Http\Requests\PlatformRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Platform;
use App\Repositories\PlatformRepository;
use App\Services\LearningAreaAPI\LearningAreaService;
use App\Services\Platform\PlatformService;
use App\Services\Storage\UploadedImage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class PlatformController extends Controller
{
    use CustomResponseTrait;

    private PlatformService $platformService;
    private PlatformRepository $platformRepository;

    public function __construct(
        PlatformService $platformService,
        PlatformRepository $platformRepository
    ) {
        $this->platformService = $platformService;
        $this->platformRepository = $platformRepository;
        Intl::ptBR();
    }

    /**
     * Display all platforms.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;

            $platforms = $this->platformService->getPlatforms($request->all());
            $data = ['platforms' => CollectionHelper::paginate($platforms, $offset)];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Store new platform
     *
     * @param PlatformRequest $request
     * @return JsonResponse
     */
    public function store(PlatformRequest $request)
    {
        try {
            $this->platformRepository->createPlatform($request);
            return $this->customJsonResponse('Dados cadastrado com sucesso.', 201);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400);
        }
    }



    /**
     * Get platform by ID
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $platform = $this->platformService->getPlatformById($id);
            $data = ['platform' => $platform];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400);
        }
    }

    /**
     * Update the platform.
     *
     * @param PlatformRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(PlatformRequest $request, string $id): JsonResponse
    {
        try {
            $platform = $this->platformService->update($id, $request->validated(), $request->file('cover'));
            $data = ['platform' => $platform];

            return $this->customJsonResponse('Dados atualizados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->platformService->delete($id);

            return $this->customJsonResponse('Plataforma excluida com sucesso.', 204);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Summary // TODO REVER
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $summary = $this->platformService->getPlatformSummary($request->only('period'));
            return $this->customJsonResponse(
                'Resumo de vendas.',
                200,
                [
                    'summary' => $summary,
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get Permisions by Platform
     * @param $id
     * @return JsonResponse
     */
    public function getPermissions($id): JsonResponse
    {
        try {
            $permissions = $this->platformService->getPlatformById($id)->permissions()->get();
            $data = ['permissions' => $permissions];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get Products by Platform
     *
     * @param string $id
     * @return JsonResponse
     */
    public function getProducts(string $id): JsonResponse
    {
        try {
            $products = $this->platformService->getProducts($id);
            $data = ['products' => $products];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * List platforms
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $platforms = $this->platformService->list($request->only('search'));
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'platforms' => $platforms,
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get platform for select input
     * @param Request $request
     * @return JsonResponse
     */
    public function getByName(Request $request): JsonResponse
    {
        try {
            $platforms = $this->platformService->list($request->all());
            return $this->customJsonResponse('Plataformas encontradas', 200, ['platforms' => $platforms]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }
}
