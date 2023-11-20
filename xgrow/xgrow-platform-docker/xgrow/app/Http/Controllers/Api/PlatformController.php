<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Platform;
use App\Repositories\Platforms\PlatformRepository;
use App\Services\Storage\UploadedImage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlatformController extends Controller
{
    use CustomResponseTrait;

    private PlatformRepository $platformRepository;

    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    /**
     * Search platform
     * @param Request $request
     * @return JsonResponse
     */
    public function searchPlatforms(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $searchTerm = $request->input('search') ?? null;
            $platforms = $this->platformRepository->getCollaborationPlatforms($searchTerm)->get();
            $ownerPlatforms = $this->platformRepository->getOwnerPlatforms($searchTerm)->get();

            $data = CollectionHelper::paginate($platforms, $offset);
            $ownerData = CollectionHelper::paginate($ownerPlatforms, $offset);

            $data = ['platforms' => $data, 'ownerPlatforms' => $ownerData];

            return $this->customJsonResponse('Dados carregados com sucesso!', 200, $data);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage() . ' ' . $e->getLine(), 400, []);
        }
    }

    /**
     * Change Thumb platform
     * @param Request $request
     * @return JsonResponse
     */
    public function changePlatformImg(Request $request): JsonResponse
    {
        try {
            $platform = Platform::findOrFail($request->platformId);
            if ($request->hasFile('image')) {
                $uploadImage = new UploadedImage($request->platformId, $request->file('image'), Storage::disk('images'));
                $stored = $uploadImage->store();
                $platform->cover = $stored->converted;
                $platform->save();
            }

            return $this->customJsonResponse('Dados alterados com sucesso!');
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage() . ' ' . $e->getLine(), 400, []);
        }
    }
}
