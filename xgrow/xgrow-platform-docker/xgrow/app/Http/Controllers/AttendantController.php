<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Attendant;
use App\Audience;
use App\Helpers\SecurityHelper;
use App\Services\Callcenter\CallcenterService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
use Exception;
use Illuminate\Validation\Rule;
use stdClass;

class AttendantController extends Controller
{
	private $attendant;
    private $audience;
    private $attendance;
    private $callcenterService;

	public function __construct(Attendant $attendant, Audience $audience, Attendance $attendance, CallcenterService $callcenterService)
	{
		$this->attendant = $attendant;
        $this->audience = $audience;
        $this->attendance = $attendance;
        $this->callcenterService = $callcenterService;
	}

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendants = $this->searchData()->get();
        $audiences = $this->audience->select()->where('platform_id', '=', Auth::user()->platform_id)->get();

        $availabeAudience = array_map(
            function ($audience) {
                return $audience['callcenter_active'] !== false;
            },
            $audiences->toArray()
        );

        $numberAttendants = $attendants->count();

        $availabeAudience = count(array_unique($availabeAudience)) === 1 && array_unique($availabeAudience) === true ? false : true;

        return view('callcenter.attendants.index', compact('attendants', 'availabeAudience', 'numberAttendants'));
    }

    public function contentData(Request $request)
    {
        $query = $this->searchData();

        return datatables()->eloquent($query)->make();
    }

    private function searchData(){
        return $this->attendant
            ->withTrashed()
            ->select(
                DB::Raw("
                    attendants.id, attendants.name, attendants.email,
                    IF( attendants.deleted_at IS NULL, 'active', 'lock') AS trashed,
                    IF( attendants.allaudience = 1, 'Todos públicos', GROUP_CONCAT(audiences.name SEPARATOR ', ')) AS audience")
            )
            ->leftJoin('attendant_audience','attendant_audience.attendant_id', 'attendants.id')
            ->leftJoin('audiences','attendant_audience.audience_id', 'audiences.id')
            ->where('attendants.platform_id', Auth::user()->platform_id)
            ->groupBy('attendants.id')
            ->orderBy('attendants.created_at', 'DESC');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attendant = new stdClass;
        $attendant->id = 0;
        $attendant->active = 1;
        $attendant->allaudience = 0;

        $params_route = ['method' => 'post', 'route' => ['attendant.store']];

        $audiences = $this->audience
            ->where('platform_id', Auth::user()->platform_id)
            ->get();

        return view('callcenter.attendants.create', compact('attendant', 'audiences', 'params_route'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $attendant = $this->save($request);

        return redirect()->route('attendant.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Attendant $attendant
     * @return \Illuminate\Http\Response
     */
    public function show(Attendant $attendant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Attendant $attendant
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendant $attendant)
    {

        $params_route = [
            'method' => 'put',
            'route' => ['attendant.update', $attendant->id],
            'enctype' => 'multipart/form-data'
        ];

        $audiences = $this->audience
            ->where('platform_id', Auth::user()->platform_id)
            ->get();

        return view('callcenter.attendants.create', compact('attendant', 'params_route', 'audiences'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Attendant $attendant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendant $attendant)
    {
        $attendant = $this->save($request, $attendant->id);

        return redirect()->route('attendant.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Attendant $attendant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendant $attendant)
    {
        try {

            (new SecurityHelper)->securityUser($attendant);

            $attendant->attendancesWithoutGlobalScopes()
                ->where('status', 'pending')
                ->delete();

            $attendant->delete();

            return response()->json(['response' => 'success']);
        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    /**
    * Restore the specified resource from storage.
    *
    * @param \App\Attendant $attendant
    * @return \Illuminate\Http\Response
    */
    public function restore($id)
    {
        try {

            $attendant = $this->attendant->withTrashed()->find($id);

            (new SecurityHelper)->securityUser($attendant);

            $attendant->restore();

            return response()->json(['response' => 'success']);
        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()]);
        }
    }

     /**
     * Save the data.
     *
     * @param \App\Quiz $attendant
     * @return \Illuminate\Http\Response
     */
    public function save($request, $id = 0)
    {

        $active = isset($request->active) ? 1 : 0;
        $allaudience = isset($request->allaudience) ? 1 : 0;

        $rules = array(
            'email' => [
                'required',
                Rule::unique('attendants')->where(function ($q) use ($id) {
                    $q->where('id', '!=', $id)
                        ->where('platform_id', '=', Auth::user()->platform_id);
                })
            ]
        );

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $uuid = (string) Uuid::generate(4);

        if($id == 0)
            $request->request->add(['uuid' => $uuid]);

        $request->request->add(['allaudience' => $allaudience]);
        $request->request->add(['active' => $active]);
        $request->request->add(['platform_id' => Auth::user()->platform_id]);

        $attendant = $this->attendant->updateOrCreate(
            ['id' => $id],
            $request->all()
        );

        if($allaudience == 1)
            $attendant->audiences()->sync(null);
        else {
            if(!$request->has('audiences')){
                $attendant->audience_id = null;
                $attendant->save();
            }
            $this->callcenterService->resetAttendances($attendant);
            $attendant->audiences()->sync($request->audiences);
        }
    }

    public function sendMailLinkCallcenter($id){

        try {

            $attendant = $this->attendant->find($id);

            if(Auth::user()->platform_id != $attendant->platform_id)
                return response()->json([], 401);

            $emailService = new EmailService();
            $emailService->sendMailLinkCallcenter($attendant);

        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()]);
        }


    }


}

