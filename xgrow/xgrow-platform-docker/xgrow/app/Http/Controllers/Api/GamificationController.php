<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Gamification\GamificationService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GamificationController extends Controller
{
    use CustomResponseTrait;

    private GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /** Start page for gamefication
     * @return View|Factory
     */
    public function index()
    {
        return view('gamification.dashboard');
    }

    /** Configurations Screen
     * @return View|Factory
     */
    public function configurations()
    {
        return view('gamification.configurations');
    }

    /** Challenge Screen
     * @return View|Factory
     */
    public function challenges()
    {
        return view('gamification.challenges');
    }

    /** Report Screen
     * @return View|Factory
     */
    public function reports()
    {
        return view('gamification.reports');
    }

    /** Get the list of gamification status
     * @return JsonResponse
     */
    public function getStatus(): JsonResponse
    {
        try {
            $data = [
                'coinsEarned' => rand(2000, 10000),
                'coinsAverage' => rand(50, 200),
                'engagement' => rand(60, 99)
            ];

            return $this->customJsonResponse('', 200, $data);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Get the list of outstanding subscribers
     * @return JsonResponse
     */
    public function getOutstanding(): JsonResponse
    {
        try {
            $names = ['Jhon Doe', 'Joseph Try', 'Mary Sue'];
            $img = 'xgrow-vendor/assets/img/big-file.png';

            $winners = [];
            for ($i = 1; $i <= 3; $i++) {
                $winner = [
                    'place' => $i,
                    'name' => $names[rand(0, 2)],
                    'img' => rand(0, 1) > 0 ? $img : '',
                    'coins' => rand(200, 1000)
                ];
                array_push($winners, $winner);
            }

            $leaderboard = [];
            for ($i = 4; $i <= 9; $i++) {
                $sub = [
                    'place' => $i,
                    'name' => $names[rand(0, 2)],
                    'img' => rand(0, 1) > 0 ? $img : '',
                    'coins' => rand(200, 1000)
                ];
                array_push($leaderboard, $sub);
            }

            $outstanding = [
                'winners' => $winners,
                'leaderboard' => $leaderboard
            ];

            return $this->customJsonResponse('', 200, $outstanding);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Get the list of subscribers with no engagement
     * @return JsonResponse
     */
    public function getNoEngagement(): JsonResponse
    {
        try {
            $names = ['Jhon Doe', 'Joseph Try', 'Mary Sue'];
            $img = 'xgrow-vendor/assets/img/big-file.png';

            $noengagement = [];
            for ($i = 1; $i <= 8; $i++) {
                $sub = [
                    'place' => $i,
                    'name' => $names[rand(0, 2)],
                    'img' => rand(0, 1) > 0 ? $img : '',
                    'coins' => rand(0, 199)
                ];
                array_push($noengagement, $sub);
            }

            return $this->customJsonResponse('', 200, $noengagement);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function getChallenges($type = "least")
    {
        try {

            $req = $this->gamificationService->getChallenges();

            if (!isset($req->data)) {
                return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['data' => $req]);
            }

            $challenges = collect($req->data);

            // Sort collection based in the type
            if ($type === "least") {
                $sorted = $challenges->sortBy('totalCompletions')->flatten();
            } else if ($type === "most") {
                $sorted = $challenges->sortByDesc('totalCompletions')->flatten();
            } else {
                return $this->customJsonResponse('Precisa indicar se é do tipo "least" ou "most"', 400);
            }

            // Define the number of items that will get
            if (count($sorted) >= 20) {
                $qtdItems = 10;
            } else if ((count($sorted) % 2) != 0) {
                $qtdItems = $type === "least" ? round(count($sorted) / 2) - 1 : round(count($sorted) / 2);
            } else {
                $qtdItems = count($sorted) / 2;
            }

            $sliced = array_slice($sorted->toArray(), 0, $qtdItems);
            $data = [
                "labels" => [],
                "values" => [],
            ];

            foreach ($sliced as $item) {
                array_push($data["labels"], $item->title);
                array_push($data["values"], $item->totalCompletions);
            }

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Return Gamification settings
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getSettings(): JsonResponse
    {
        try {
            $req = $this->gamificationService->getSettings();
            return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['data' => isset($req->data) ? $req->data : $req]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Save Gamification settings
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function saveSettings(Request $request): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->saveSettings($request);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Configurações salvas com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }


    /** Return all levels
     * @return JsonResponse|string
     * @throws GuzzleException
     */
    public function getPhases()
    {
        try {
            $req = $this->gamificationService->getPhases();
            return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['data' => isset($req->data) ? $req->data : $req]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /** Save level
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function savePhase(Request $request): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->savePhase($request);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Fase salva com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Delete level by id
     * @param string $id
     * @return JsonResponse
     */
    public function deletePhase(string $id): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->deletePhase($id);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Fase excluída com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Update level by id
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updatePhase(Request $request, string $id): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->updatePhase($request, $id);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Fase atualizada com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Return all challenges by order
     * @param Request $request
     * @return JsonResponse|string
     * @throws GuzzleException
     */
    public function getChallengesDatatable(Request $request)
    {
        try {

            $offset = $request->input('offset') ?? 25;
            $page = $request->input('page') ?? 1;

            $challenges = collect($this->gamificationService->getChallenges()->data)->sortBy('order');

            $filterSearch = $request->search ?? null;

            if ($filterSearch) {
                $challenges = $challenges->filter(function ($res) use ($filterSearch) {
                    return str_contains(strtolower($res->title), strtolower($filterSearch));
                });
            }

            $data = CollectionHelper::paginate($challenges, $offset);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Save challenge
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function saveChallenge(Request $request): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->saveChallenge($request);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Desafio salvo com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Delete challenge by id
     * @param string $id
     * @return JsonResponse
     */
    public function deleteChallenge(string $id): JsonResponse
    {
        try {

            $data = (object)$this->gamificationService->deleteChallenge($id);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Desafio excluído com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Update challenge by id
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateChallenge(Request $request, string $id): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->updateChallenge($request, $id);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Desafio atualizado com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Return Challenge settings
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getChallengeSettings(): JsonResponse
    {
        try {
            $req = $this->gamificationService->getChallengeSettings();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['data' => $req->data]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Save challenge settings
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function saveChallengeSettings(Request $request): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->saveChallengeSettings($request);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Configurações salvas com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Update challenge settings by id
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateChallengeSettings(Request $request, string $id): JsonResponse
    {
        try {
            $data = (object)$this->gamificationService->updateChallengeSettings($request, $id);
            if (isset($data->error)) throw new Exception(ucfirst(json_decode($data->message)->message) . '.', 400);

            return $this->customJsonResponse('Configurações atualizadas com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Get all posible actions (score)
     * @return JsonResponse
     */
    public function getActions(): JsonResponse
    {
        try {
            $req = $this->gamificationService->getActions();
            return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['data' => isset($req->data) ? $req->data : $req]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /** Save action list (score)
     * @return JsonResponse
     */
    public function saveActions(Request $request): JsonResponse
    {
        try {
            $req = $this->gamificationService->saveActions($request);
            return $this->customJsonResponse('Sua nova configuração de pontuação foi salva.', 200, ['data' => isset($req->data) ? $req->data : $req]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }
}
