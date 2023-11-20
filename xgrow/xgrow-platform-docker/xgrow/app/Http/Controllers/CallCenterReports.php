<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AttendanceContact;
use App\Attendant;
use App\Audience;
use App\AudienceCondition;
use App\Repositories\Campaign\AudienceConditionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Clue\StreamFilter\fun;

class CallCenterReports extends Controller
{
    private $attendance;
    private $attendanceContact;
    private $attendant;
    private $audience;
    private $audienceCondition;
    private $repo;

    public function __construct(
        Attendant $attendant,
        Attendance $attendance,
        AttendanceContact $attendanceContact,
        Audience $audience,
        AudienceCondition $audienceCondition,
        AudienceConditionRepository $repo
    )
    {
        $this->attendant = $attendant;
        $this->attendance = $attendance;
        $this->attendanceContact = $attendanceContact;
        $this->audience = $audience;
        $this->audienceCondition = $audienceCondition;
        $this->repo = $repo;
    }

    public function index()
    {
        $data["audiences_filter"] = $this->audience->select('name')->where('audiences.platform_id', Auth::user()->platform_id)->get();
        $data["attendants_filter"] = $this->attendant->select('name')->where('attendants.platform_id', Auth::user()->platform_id)->get();
        $data["status_filter"] = Attendance::allStatus();

        return view('callcenter.reports.index', $data);
    }

    public function dashboard()
    {
        $data["audiences_filter"] = $this->audience->select('id', 'name', 'callcenter_active')->where('audiences.platform_id', Auth::user()->platform_id)->get();

        return view('callcenter.dashboard.index', $data);
    }

    public function show($id, $type = null)
    {
        $attendance_contacts = $this->attendanceContact
            ->select(
                'id',
                'attendance_id',
                'status',
                'description',
                'reasons_loss_id',
                'ip',
                'created_at'
            )
            ->with([
                'attendance' => function ($query) {
                    $query
                    ->select([
                        'id',
                        'attendant_id',
                        'subscriber_id',
                        'audience_id',
                        'status'
                    ])
                    ->with([
                        'attendant' => function ($query) {
                            $query
                            ->select(
                                'id',
                                'name'
                            )
                            ->where('platform_id', Auth::user()->platform_id);
                        },
                        'subscriber' => function ($query) {
                            $query
                            ->select(
                                'id',
                                'name'
                            )
                            ->where('platform_id', Auth::user()->platform_id);
                        },
                        'audience' => function ($query) {
                            $query
                            ->select(
                                'id',
                                'name'
                            )
                            ->where('platform_id', Auth::user()->platform_id);
                        }
                    ]);
                },
                'reasons_loss' => function ($query) {
                    $query
                    ->select([
                        'id',
                        'description'
                    ]);
                }
            ])
            ->where('attendance_id', $id)
            ->get();

        $data["attendance_id"] = $id;
        $data["attendance_contacts"] = $attendance_contacts;
        $data["status_filter"] = Attendance::allStatus();

        if ($type === "request") {
            return response()->json($data, 200);
        }

        return view('callcenter.reports.report', $data);
    }

    public function infoPerAttendant($condition, $period, $audiences)
    {
        $period = explode(" ", $period);
        $inital = $period[0].' 00:00:00';
        $final = $period[2].' 23:59:59';
        $attendants = $this->attendance
            ->select([
                'attendant_id',
                'audience_id',
                DB::raw("COUNT(case status when '{$condition}' then 1 else null end) as counted_info")
            ])
            ->with([
                'audience' => function ($query) {
                    $query
                    ->select(
                        'id',
                        'name'
                    );
                },
                'attendant' => function ($query) {
                    $query
                    ->select(
                        'id',
                        'name'
                    )
                    ->where('platform_id', Auth::user()->platform_id);
                },
            ])
            ->where('status', '=', $condition)
            ->where('updated_at', '>=', $inital)
            ->where('updated_at', '<=', $final)
            ->groupBy('attendant_id');

        $audiences = explode(',', $audiences);
        $attendants = $attendants->where(function ($q) use ($audiences) {
            foreach ($audiences as $audience) {
                $result = $this->audience->select()->where('name', '=', $audience)->first();

                if (!empty($result)) {
                    $q = $q->orWhere('audience_id', '=', $result->id);
                }
            }
        });

        $attendants = $attendants->get()->reject(function ($attendance) {
            return is_null($attendance->attendant);
        });

        $total = 0;
        foreach ($attendants as $attendant) {
            $total += $attendant->counted_info;
        }

        $data["group"] = $attendants;
        $data["total"] = $total;

        return response()->json($data, 200);
    }

    public function getTotalPending($audiences)
    {
        $totalLeads = json_decode($this->getTotalLeads($audiences)->getContent());

        $totalPending = $this->attendance
            ->select()
            ->with([
                'attendant' => function ($q) {
                    $q
                    ->select([
                        'id',
                        'platform_id'
                    ])
                    ->where('platform_id', Auth::user()->platform_id);
                }
            ])
            ->with('audience')
            ->where('status', '<>', 'pending');

        $audiences = $audiences != null ? explode(',', $audiences) : [];
        $totalPending = $totalPending->where(function ($q) use ($audiences) {
            foreach ($audiences as $audience) {
                $result = $this->audience->select()->where('name', '=', $audience)->first();

                if (!empty($result)) {
                    $q = $q->orWhere('audience_id', '=', $result->id);
                }
            }
        });

        $totalPending = $totalPending->get()->reject(function ($pending) {
            return is_null($pending->attendant);
        })->count();

        $totalPending = ($totalLeads - $totalPending) > 0 ? ($totalLeads - $totalPending) : 0;

        return response()->json($totalPending, 200);
    }

    public function getTotalLeads($audiences)
    {
        $audiences = explode(',', $audiences);
        $audienceWithConditions = $this->audience->select()
                        ->with(['audienceConditions'])
                        ->where('platform_id', '=', Auth::user()->platform_id)
                        ->whereIn('name', $audiences)
                        ->get();

        $leads = [];
        foreach ($audienceWithConditions as $audienceWithCondition) {
            array_push(
                $leads,
                $this->repo->generateQueryFromArray(
                    Auth::user()->platform_id,
                    $audienceWithCondition->audienceConditions->toArray()
                )->get()->count()
            );
        }

        return response()->json(array_sum($leads), 200);
    }

    public function getAttendantsList($audiences)
    {
        $audiences = explode(',', $audiences);
        $sqlAudiences = "(";
        foreach ($audiences as $audience) {
            $result = $this->audience->select()->where('name', '=', $audience)->first();

            if (!empty($result)) {
                $sqlAudiences .= "$result->id, ";
            }
        }
        $sqlAudiences = rtrim($sqlAudiences, ", ") . ")";
        $platform_id = Auth::user()->platform_id;
        $attendants =
            DB::select(
                DB::raw(
                    "SELECT *
                    FROM
                    (
                        SELECT `attendants`.`id`, `attendants`.`name`, `attendants`.`email`
                        FROM attendants
                        JOIN attendant_audience
                            ON `attendants`.`id` = `attendant_audience`.`attendant_id`
                        JOIN audiences
                            ON `attendant_audience`.`audience_id` = `audiences`.`id`
                        WHERE `audiences`.`id` IN $sqlAudiences AND `attendants`.`platform_id` = '$platform_id'
                        UNION ALL
                        SELECT `attendants`.`id`, `attendants`.`name`, `attendants`.`email`
                        FROM attendants
                        WHERE `allaudience` = true AND `attendants`.`platform_id` = '$platform_id'
                    ) AS result
                    GROUP BY result.`id`
                    ORDER BY result.`id`"
                )
            );

        return response()->json($attendants, 200);
    }

    public function getReportsList($id = null)
    {
        // "GROUP BY MAX ID" TO GET THE LAST CONTACT
        $attendance_contact_ids = array_map(function($value) {
            return $value->max_id;
        }, DB::select('SELECT max(id) AS "max_id" FROM attendance_contacts GROUP BY attendance_id'));

        $attendances = $this->attendanceContact
            ->select(
                'id',
                'attendance_id',
                'status',
                'description',
                'reasons_loss_id',
                'ip',
                'created_at',
            )
            ->with([
                'attendance' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'attendant_id',
                            'subscriber_id',
                            'audience_id',
                            'status'
                        ])
                        ->with([
                            'attendant' => function ($query) {
                                $query
                                    ->select(
                                        'id',
                                        'name'
                                    )
                                    ->where('platform_id', Auth::user()->platform_id);
                            },
                            'subscriber' => function ($query) {
                                $query
                                    ->select(
                                        'id',
                                        'email',
                                        'name'
                                    )
                                    ->where('platform_id', Auth::user()->platform_id);
                            },
                            'audience' => function ($query) {
                                $query
                                    ->select(
                                        'id',
                                        'name'
                                    )
                                    ->where('platform_id', Auth::user()->platform_id);
                            }
                        ])
                        ->withoutGlobalScopes();
                },
                'reasons_loss' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'description'
                        ]);
                }
            ])
            ->whereIn('id', $attendance_contact_ids)
            ->get()->reject(function ($attendance_contact) {
                return
                        is_null($attendance_contact->attendance->attendant) ||
                        is_null($attendance_contact->attendance->subscriber) ||
                        is_null($attendance_contact->attendance->audience)
                    ;
            });

        if ($id !== null) {
            $attendances = $attendances->where('attendance.attendant.id', '=', $id)->flatten();
        }

        $data["data"] = $attendances->flatten();
        $data["totalLabel"] = getTotalLabel($data["data"], 'relatório');

        return response()->json($data, 200);
    }

    public function publicReports()
    {
        return view('callcenter.reports.public');
    }

    public function getPublicReports()
    {
        $ended_audiences = $this->audience->select('id')->where('callcenter_active', '=', false)->get();

        $audienceWithConditions = $this->audience->select()
            ->with(['audienceConditions'])
            ->where('platform_id', '=', Auth::user()->platform_id)
            ->whereIn('id', $ended_audiences)
            ->get();

        $leads = [];
        foreach ($audienceWithConditions as $audienceWithCondition) {
            array_push(
                $leads,
                $this->repo->generateQueryFromArray(
                    Auth::user()->platform_id,
                    $audienceWithCondition->audienceConditions->toArray()
                )->get()->count()
            );
        }

        $attendances = $this->attendance
            ->select([
                'id',
                'audience_id',
                DB::raw("COUNT(case status when 'gain' then 1 else null end) as number_gain"),
                DB::raw("COUNT(case status when 'lost' then 1 else null end) as number_lost"),
                DB::raw("COUNT(case status when 'contactless' then 1 else null end) as number_contactless"),
                DB::raw("COUNT(case status when 'pending' then 0 else 1 end) as aux_pending")
            ])
            ->with([
                'contacts' => function ($q) {
                    $q
                    ->select([
                        'id',
                        'attendance_id',
                        'created_at'
                    ])
                    ->groupBy('attendance_id');
                },
                'audience' => function ($q) {
                    $q
                    ->select([
                        'id',
                        'name',
                        'callcenter_end_date',
                    ])
                    ->where('platform_id', '=', Auth::user()->platform_id);
                }
            ])
            ->whereIn('audience_id', $ended_audiences)
            ->groupBy('audience_id')
            ->get();

        $filteredAttendances = $attendances->filter(function ($attendance, $key) {
            return !empty($attendance->audience);
        });

        $audiences = [];
        $i = 0;
        foreach ($filteredAttendances as $attendance) {
            $audience['id'] = $attendance->audience->id;
            $audience['name'] = $attendance->audience->name;
            $audience['init_date'] = count($attendance->contacts) > 0 ? date('Y-m-d H:i:s', strtotime($attendance->contacts[0]->created_at)) : null;
            $audience['end_date'] = $attendance->audience->callcenter_end_date;
            $audience['number_leads'] = $leads[$i];
            $audience['number_pending'] = ($leads[$i] - $attendance->aux_pending) > 0 ? ($leads[$i] - $attendance->aux_pending) : 0;
            $audience['number_gain'] = $attendance->number_gain;
            $audience['number_lost'] = $attendance->number_lost;
            $audience['number_contactless'] = $attendance->number_contactless;
            $audience['number_attendants'] = 0;

            array_push($audiences, (Object) $audience);
            $i++;
        }

        $attendants = $this->attendant
            ->select()
            ->with([
                'audiences' => function ($q) {
                    $q
                    ->select(['id']);
                }
            ])
            ->where('platform_id', '=', Auth::user()->platform_id)
            ->get();

        foreach ($attendants as $attendant) {
            $attAudience = $attendant->audiences->map(function ($audience) {
                return $audience->id;
            });

            foreach ($audiences as $audience) {
                if ($attendant->allaudience == 1 || in_array($audience->id, $attAudience->toArray())) {
                    $audience->number_attendants++;
                }
            }
        }

        $data["data"] = $audiences;
        $data["totalLabel"] = count($audiences) . (count($audiences) == 1 ? ' público' : ' públicos');

        return response()->json($data, 200);
    }
}
