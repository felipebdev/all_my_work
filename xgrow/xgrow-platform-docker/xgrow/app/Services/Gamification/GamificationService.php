<?php

namespace App\Services\Gamification;

use App\Services\LAService;
use App\Services\Storage\UploadedImage;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;

class GamificationService
{
    /**
     * Return the LA Service connection
     * @return LAService
     */
    private function laService(): LAService
    {
        $platform_id = Auth::user()->platform_id;
        $user_id = Auth::user()->id;
        return new LAService($platform_id, $user_id);
    }

    /** Return Gamification settings
     * @return mixed
     * @throws GuzzleException
     */
    public function getSettings()
    {
        try {
            return $this->laService()->get('/gamification/settings');
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    /** Save Gamification settings
     * @param $request
     * @return mixed
     * @throws GuzzleException
     */
    public function saveSettings($request)
    {
        try {
            $data = [
                'isEnabled' => $request->input('isEnabled') === 'true',
                'showBestPlayersRanking' => $request->input('showBestPlayersRanking') === 'true',
                'showWorsePlayersRanking' => $request->input('showWorsePlayersRanking') === 'true',
                'showPoints' => $request->input('showPoints') === 'true',
                'showPhases' => $request->input('showPhases') === 'true',
                'showChallengesReward' => $request->input('showChallengesReward') === 'true',
            ];

            return $this->laService()->post('/gamification/settings', json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /** Return all levels
     * @return mixed
     * @throws GuzzleException
     */
    public function getPhases()
    {
        try {
            return $this->laService()->get('/gamification/phases', ['platformId' => Auth::user()->platform_id,]);
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    /** Save level data
     * @param mixed $request
     * @return mixed
     * @throws GuzzleException
     */
    public function savePhase($request)
    {
        try {
            $uploadImage = new UploadedImage(Auth::user()->platformId, $request->file('iconUrl'), Storage::disk('images'));
            $storedImage = $uploadImage->store();
            $data = [
                'name' => $request->input('name'),
                'requiredPoints' => $request->input('requiredPoints'),
                'color' => $request->input('color'),
                'iconUrl' => $storedImage->converted,
                'platformId' => Auth::user()->platform_id,
                'order' => $request->input('order'),
            ];

            return $this->laService()->post('/gamification/phases', json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /** Update level data
     * @param $request
     * @param string $id
     * @return array|mixed|ResponseInterface|null
     */
    public function updatePhase($request, string $id)
    {
        try {
            $data = [
                'name' => $request->input('name'),
                'requiredPoints' => $request->input('requiredPoints'),
                'color' => $request->input('color'),
                'platformId' => Auth::user()->platform_id,
                'order' => $request->input('order'),
            ];

            if ($request->file('iconUrl')) {
                $uploadImage = new UploadedImage(Auth::user()->platformId, $request->file('iconUrl'), Storage::disk('images'));
                $storedImage = $uploadImage->store();
                $data['iconUrl'] = $storedImage->converted;
            }else{
                $data['iconUrl'] = $request->input('iconUrl');
            }

            return $this->laService()->put('/gamification/phases/' . $id, json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        } catch (GuzzleException $e) {
            return $e->getResponse();
        }
    }

    /** Delete Level by id
     * @param $id
     * @return array|mixed|ResponseInterface|null
     * @throws GuzzleException
     */
    public function deletePhase($id)
    {
        try {
            $data = ['platformId' => Auth::user()->platform_id];
            return $this->laService()->delete('/gamification/phases/' . $id, json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /** Return challenges
     * @return mixed
     * @throws GuzzleException
     */
    public function getChallenges()
    {
        try {
            return $this->laService()->get('/producer/gamification/challenges');
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }


    /** Save challenge data
     * @param mixed $request
     * @return mixed
     * @throws GuzzleException
     */
    public function saveChallenge($request)
    {
        try {

            $data = [
                'title' => $request->input('title'),
                'message' => $request->input('message'),
                'multimediaType' => $request->input('multimediaType'),
                'multimediaUrl' => $request->input('multimediaUrl'),
                'answerType' => $request->input('answerType'),
                'specificDate' => $request->input('specificDate'),
                'showOnLogin' => $request->input('showOnLogin'),
                'platformId' => Auth::user()->platform_id,
                'order' => $request->input('order'),
                'reward' => $request->input('reward'),
                'optionsList' => $request->input('optionsList')
            ];

            return $this->laService()->post('/producer/gamification/challenges', json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /** Update challenge data
     * @param $request
     * @param string $id
     * @return array|mixed|ResponseInterface|null
     */
    public function updateChallenge($request, string $id)
    {
        try {
            $data = [
                'title' => $request->input('title'),
                'message' => $request->input('message'),
                'multimediaType' => $request->input('multimediaType'),
                'multimediaUrl' => $request->input('multimediaUrl'),
                'answerType' => $request->input('answerType'),
                'specificDate' => $request->input('specificDate'),
                'showOnLogin' => $request->input('showOnLogin'),
                'platformId' => Auth::user()->platform_id,
                'order' => $request->input('order'),
                'reward' => $request->input('reward'),
                'optionsList' => $request->input('optionsList'),
                'id' => $id
            ];

            return $this->laService()->post('/producer/gamification/challenges', json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        } catch (GuzzleException $e) {
            return $e->getResponse();
        }
    }

    /** Delete challenge by id
     * @param $id
     * @return array|mixed|ResponseInterface|null
     * @throws GuzzleException
     */
    public function deleteChallenge($id)
    {
        try {
            $data = ['platformId' => Auth::user()->platform_id];
            return $this->laService()->delete('/producer/gamification/challenges/' . $id, json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /** Return challenges
     * @return mixed
     * @throws GuzzleException
     */
    public function getChallengeSettings()
    {
        try {
            return $this->laService()->get('/producer/gamification/challenges-settings');
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    /** Save challenge settings
     * @param $request
     * @return mixed
     * @throws GuzzleException
     */
    public function saveChallengeSettings($request)
    {
        try {
            $data = [
                'platformId' => Auth::user()->platform_id,
                'enableChallenges' => $request->input('enableChallenges') === true,
                'formDelivery' => $request->input('formDelivery'),
                'deliveryFrequency' => $request->input('deliveryFrequency'),
                'frequencyFormat' => $request->input('frequencyFormat'),
                'startFrom' => $request->input('startFrom'),
            ];

            return $this->laService()->post('/producer/gamification/challenges-settings', json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /** Update challenge settings data
     * @param $request
     * @param string $id
     * @return array|mixed|ResponseInterface|null
     */
    public function updateChallengeSettings($request, string $id)
    {
        try {
            $data = [
                'enableChallenges' => $request->input('enableChallenges'),
                'formDelivery' => $request->input('formDelivery'),
                'deliveryFrequency' => $request->input('deliveryFrequency'),
                'frequencyFormat' => $request->input('frequencyFormat'),
                'startFrom' => $request->input('startFrom'),
            ];

            return $this->laService()->put('/producer/gamification/challenges-settings/' . $id, json_encode($data));
        } catch (RequestException $e) {
            return $e->getResponse();
        } catch (GuzzleException $e) {
            return $e->getResponse();
        }
    }

    /** Get all possible actions
     * @return array|mixed|string
     * @throws GuzzleException
     */
    public function getActions()
    {
        try {
            $actions = $this->laService()->get('/gamification/actions-list');
            $suggestions = $this->laService()->get('/gamification/possible-actions');
            return ['actions' => $actions, 'suggestions' => $suggestions];
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    /** Save actions data
     * @param mixed $request
     * @return mixed
     * @throws GuzzleException
     */
    public function saveActions($request)
    {
        try {
            $data['actionList'] = json_decode($request->actionList);
            return $this->laService()->post('/gamification/actions-list', json_encode($data));
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }
}
