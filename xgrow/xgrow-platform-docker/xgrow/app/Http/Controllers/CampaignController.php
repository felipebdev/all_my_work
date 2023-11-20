<?php

namespace App\Http\Controllers;

use App\Audience;
use App\Campaign;
use App\Course;
use App\File;
use App\Platform;
use App\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;

class CampaignController extends Controller
{
    private $campaign;
    private $section;
    private $course;
    private $platform;
    private $audience;

    public function __construct(
        Campaign $campaign,
        Section $section,
        Course $course,
        Platform $platform,
        Audience $audience
    ) {
        $this->campaign = $campaign;
        $this->section = $section;
        $this->course = $course;
        $this->platform = $platform;
        $this->audience = $audience;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = $this->searchData()->get();
        $audiences = Audience::where('platform_id', Auth::user()->platform_id);
        return view('campaign.index', compact('campaigns', 'audiences'));
    }


    public function contentData(Request $request)
    {
        $query = $this->searchData();

        return datatables()->eloquent($query)->make();
    }

    private function searchData(){
        $scheduled = Campaign::TYPE_SCHEDULED;
        $typeScheduled = Campaign::listTypes()[Campaign::TYPE_SCHEDULED];
        $typeAutomatic = Campaign::listTypes()[Campaign::TYPE_AUTOMATIC];
        return $this->campaign->select('campaigns.id', 'campaigns.name', 'campaigns.start_at')
            ->selectRaw("IF (type = {$scheduled}, '{$typeScheduled}', '{$typeAutomatic}') type_campaign")
            ->selectRaw("GROUP_CONCAT(audiences.name SEPARATOR '\n') as audience_names")
            ->join('campaign_audience', 'campaigns.id', '=', 'campaign_audience.campaign_id')
            ->join('audiences', 'campaign_audience.audience_id', '=', 'audiences.id')
            ->where('campaigns.platform_id', Auth::user()->platform_id)
            ->groupBy('campaigns.id')
            ->orderBy('campaigns.created_at', 'DESC');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $campaign = new stdClass;
        $campaign->id = 0;

        $campaign->has_start = 1;
        $campaign->has_finish = 0;
        $campaign->audio_id = 0;

        $campaign->start_date = '';
        $campaign->start_time = '';

        $campaign->finish_date = date('Y-m-d');
        $campaign->finish_time = "23:59";

        $campaign->type = Campaign::TYPE_SCHEDULED;

        $campaign->format = Campaign::FORMAT_EMAIL;

        $campaign->automatic_type = 0;
        $campaign->automatic_id = 0;

        $automatic_ids = [];

        $params_route = ['method' => 'post', 'route' => ['campaign.store'], 'enctype' => 'multipart/form-data'];


        $audiences = $this->audience
            ->where('platform_id', Auth::user()->platform_id)
            ->get();

        return view('campaign.create', compact('campaign', 'audiences', 'params_route', 'automatic_ids'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $campaign = $this->save($request);

        return redirect()->route('campaign.index');
    }

    /**
     * Save the data.
     *
     * @param \App\Quiz $campaign
     * @return \Illuminate\Http\Response
     */
    public function save($request, $id = 0)
    {
        $rules['automatic_type'] = 'required_if:type,' . Campaign::TYPE_AUTOMATIC;
        $rules['automatic_id'] = 'required_if:type,' . Campaign::TYPE_AUTOMATIC . '|required_if:type,>,' . Campaign::FIRST_ACCESS_TO_THE_SITE;

        if ($id == 0) {
            $status = ($request->type == Campaign::TYPE_AUTOMATIC) ? Campaign::STATUS_STARTED : Campaign::STATUS_PENDING;
            $request->request->add(['status' => $status]);
        }

        if (Campaign::listFormats()[$request->format]['msg_type'] == 2) { //mensagem html
            $rules['msg_type_html'] = 'required';
            $request->request->add(['text' => $request->msg_type_html]);
        }

        if (Campaign::listFormats()[$request->format]['subject'] == 1) { //assunto
            $rules['subject'] = 'required';
            $request->request->add(['subject' => $request->subject]);
        }

        if (Campaign::listFormats()[$request->format]['msg_type'] == 1) { //mensagem texto
            $rules['msg_type_text'] = 'required';
            $request->request->add(['text' => $request->msg_type_text]);
        }

        if (Campaign::listFormats()[$request->format]['audio'] == 1 and $id == 0) {
            $rules['upload_audio'] = 'required';
        }

        $rules['audiences'] = 'required';
        $rules['start_date'] = 'required_if:type,' . Campaign::TYPE_SCHEDULED;
        $rules['start_time'] = 'required_if:type,' . Campaign::TYPE_SCHEDULED;

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $audio = File::setUploadedAudio($request, 'upload_audio');

        $has_start = 0;
        $has_finish = 0;
        $start_at = null;
        $finish_at = null;

        if ($request->type == Campaign::TYPE_SCHEDULED) {
            $datetime = Carbon::createFromFormat('d/m/Y H:i', "{$request->start_date} {$request->start_time}");
            $start_at = $datetime->setSeconds(0)->format('Y-m-d H:i:s');
            /*
              $has_start = (isset($request->has_start)) ? 1 : 0;
              $has_finish = (isset($request->has_finish)) ? 1 : 0;
              if($has_start == 1){
                  $start_at = "{$request->start_date} {$request->start_time}";
              }
              if($has_finish == 1){
                  $finish_at = "{$request->finish_date} {$request->finish_time}";
              }
            */
        }

        $request->request->add(['platform_id' => Auth::user()->platform_id]);
        $request->request->add(['has_start' => $has_start]);
        $request->request->add(['has_finish' => $has_finish]);
        $request->request->add(['start_at' => $start_at]);
        $request->request->add(['finish_at' => $finish_at]);

        $campaign = $this->campaign->updateOrCreate(
            ['id' => $id],
            $request->all()
        );

        $campaign->audiences()->sync($request->audiences);

        File::saveUploadedFile($campaign, $audio, 'audio_id');

        return $campaign;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign)
    {
        $params_route = [
            'method' => 'put',
            'route' => ['campaign.update', $campaign->id],
            'enctype' => 'multipart/form-data'
        ];

        $campaign->msg_type_html = null;
        $campaign->msg_type_text = null;

        if (Campaign::listFormats()[$campaign->format]['msg_type'] == 2) {
            $campaign->msg_type_html = $campaign->text;
        } else {
            $campaign->msg_type_text = $campaign->text;
        }

        $campaign->start_date = null;
        $campaign->start_time = null;
        $campaign->finish_date = null;
        $campaign->finish_time = null;

        if ($campaign->type == Campaign::TYPE_SCHEDULED) {
            $start = Carbon::parse($campaign->start_at);
            $campaign->start_date = $start->format('d/m/Y');
            $campaign->start_time = $start->format('H:i');
        }

        $automatic_ids = [];

        $audiences = $this->audience
            ->where('platform_id', Auth::user()->platform_id)
            ->get();

        return view('campaign.create', compact('campaign', 'params_route', 'automatic_ids', 'audiences'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        $campaign = $this->save($request, $campaign->id);

        return redirect()->route('campaign.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        try {
            $campaign->delete();

            return response()->json(['response' => 'success']);
        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get automatics ids by automatic types.
     *
     * @param type $type
     */
    public function getAutomaticIds($type)
    {
        try {
            $platform_id = Auth::user()->platform_id;

            switch ($type) {
                case Campaign::FIRST_ACCESS_TO_THE_CONTENT:
                    $contents = $this->platform->find($platform_id)->contents();
                    $data = $contents
                        ->where('is_course', 0)
                        ->where('published', 1)
                        ->where('has_external_link', 0)
                        ->select('title as name', 'contents.id')
                        ->get();
                    break;

                case Campaign::FIRST_ACCESS_TO_THE_COURSE:
                    $data = $this->course
                        ->where('platform_id', $platform_id)
                        ->where('active', 1)
                        ->select('name', 'id')
                        ->get();
                    break;

                case Campaign::FIRST_ACCESS_TO_THE_SECTION:
                    $data = $this->section
                        ->where('platform_id', $platform_id)
                        ->where('active', 1)
                        ->select('name', 'id')
                        ->get();
                    break;
            }

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
