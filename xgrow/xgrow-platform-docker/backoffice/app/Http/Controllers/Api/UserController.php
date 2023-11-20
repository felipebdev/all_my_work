<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\{ChangeStatusRequest, IndexRequest, StoreUpdateRequest};
use App\Http\Traits\CustomResponseTrait;
use App\Services\User\UserService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var User
     */
    private $user;

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @param User $user
     * @param UserService $userService
     */
    public function __construct(User $user, UserService $userService)
    {
        $this->user = $user;
        $this->userService = $userService;
    }

    /**
     * List all users
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request){
        try {

            $offset = $request->input('offset') ?? 25;
            $admins = $this->userService->getUsers($request->only('search', 'status'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['users' => CollectionHelper::paginate($admins, $offset)]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 500, []);
        }
    }

    /**
     * Save users data
     * @param StoreUpdateRequest $request
     * @return JsonResponse
     */
    public function store(StoreUpdateRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->all());

            return $this->customJsonResponse('Usuário adicionado com sucesso.', 201, ['user' => $user]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Show users data
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $user = $this->userService->getUser($id);
            return $this->customJsonResponse(
                'Dados do usuário.',
                200,
                ['user' => $user]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 500, []);
        }
    }

    /**
     * Update users data
     * @param StoreUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(StoreUpdateRequest $request, $id): JsonResponse
    {
        try {

            $user = $this->userService->updateUser($id,$request->all());

            return $this->customJsonResponse('Usuário atualizado com sucesso.', 200, ['user' => $user]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Delete user
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return $this->customJsonResponse('Usuário removido com sucesso.', 200);
        }
        catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Change status users
     * @param ChangeStatusRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function changeStatus(ChangeStatusRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->only('active'));
            return $this->customJsonResponse('Usuário atualizado com sucesso.', 200, ['user' => $user]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * list users
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $users = $this->userService->listUsers();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['users' => $users]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

}
