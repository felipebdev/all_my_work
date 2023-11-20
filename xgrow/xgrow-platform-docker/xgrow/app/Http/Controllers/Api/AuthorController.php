<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorPost;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Author\AuthorService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    use CustomResponseTrait;

    private AuthorService $authorService;

    public function __construct(
        AuthorService $authorService
    )
    {
        $this->authorService = $authorService;
    }

    /**
     * Get authors
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $authors = $this->authorService->list($request->input('search'))->get();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['authors' => CollectionHelper::paginate($authors, $offset)]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $authors = $this->authorService->list($request)->select('id', 'name_author')->get();

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['authors' => $authors]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Dados do author
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $author = $this->authorService->show($id);
            return $this->customJsonResponse(
                'Dados do author.',
                200,
                ['author' => $author]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Remove photo from autor
     * @param Request $request
     * @return JsonResponse
     */
    public function deletePhoto(Request $request): JsonResponse
    {
        try {
            $author = $this->authorService->deletePhoto($request->input('id'));
            return $this->customJsonResponse(
                'Foto excluída com sucesso.',
                200,
                ['author_photo' => $author->author_photo]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Add new author
     * @param StoreAuthorPost $request
     * @return JsonResponse
     */
    public function store(StoreAuthorPost $request): JsonResponse
    {
        try {
            $author = $this->authorService->store($request->all(), $request->file('image'));
            return $this->customJsonResponse('Autor adicionado com sucesso.', 201, ['author' => $author]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Update new author
     * @param StoreAuthorPost $request
     * @param $id
     * @return JsonResponse
     */
    public function update(StoreAuthorPost $request, $id): JsonResponse
    {
        try {
            $author = $this->authorService->update($id,$request->all(), $request->file('image'));
            return $this->customJsonResponse('Autor atualizado com sucesso.', 201, ['author' => $author]);
        } catch (Exception | GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Change status from author
     * @param int $id
     * @return JsonResponse
     */
    public function status(int $id): JsonResponse
    {
        try {
            $author = $this->authorService->changeStatus($id);
            return $this->customJsonResponse(
                'Status atualizado com sucesso.',
                201,
                ['author' => $author->status ? 'Ativo' : 'Inativo']
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Change status from author
     * @param Request $request
     * @return JsonResponse
     */
    public function transferContent(Request $request): JsonResponse
    {
        try {
            $data = $this->authorService->transferContent(
                $request->input('origin'),
                $request->input('destination'),
                $request->input('pass')
            );
            return $this->customJsonResponse(
                "Foram atualizados {$data} registros com sucesso! A alteração pode levar até 5 minutos para refletir na Área de Aprendizado.",
                201
            );
        } catch (Exception | GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->authorService->delete($id);
            return $this->customJsonResponse('Autor removido com sucesso.', 201);
        } catch (QueryException $q) {
            return $this->customJsonResponse(
                'Não é possível remover este autor, pois existem conteúdos atrelados a ele.',
                400
            );
        }
        catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }


    /**
     * Get Author in pairs
     * Return pair information for author used in combo or select
     * @return JsonResponse
     */
    public function getAuthorToList(): JsonResponse
    {
        try {
            $authors = $this->authorService->getAuthors();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['authors' => $authors]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }


}
