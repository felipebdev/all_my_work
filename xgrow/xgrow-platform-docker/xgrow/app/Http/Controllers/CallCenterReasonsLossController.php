<?php

namespace App\Http\Controllers;

use App\CallcenterReasonsLoss;

class CallCenterReasonsLossController extends Controller
{
    public function __construct()
    {
        $this->reasonsLoss = new CallcenterReasonsLoss;
    }

    public function index($callcenter_id)
    {
        
        return $this->reasonsLoss->where('callcenter_id', '=', $callcenter_id)->get();
    }

    public function store($reasons, $callcenter_id)
    {
        foreach ($reasons as $reason) {
            if (!$this->checkIfExists($reason, $callcenter_id)) {
                $this->reasonsLoss->create([
                    'callcenter_id' => $callcenter_id,
                    'description' => $reason,
                ]);
            }
        }
    }

    public function checkIfExists($reason, $callcenter_id)
    {
        $exists = $this->reasonsLoss
            ->where('callcenter_id', '=', $callcenter_id)
            ->where('description', '=', $reason)
            ->exists();
        
        if ($exists) {
            return true;
        }

        return false;
    }

    public function update($reasons, $callcenter_id)
    {
        foreach ($reasons as $id => $description) {
            $reason = $this->reasonsLoss
                ->where('id', '=', $id)
                ->where('callcenter_id', '=', $callcenter_id)
                ->get()
                ->first();
            $reason->description = $description;
            $reason->save();
        }
    }

    public function delete($reasons, $callcenter_id)
    {
        foreach ($reasons as $reason_id) {
            $this->reasonsLoss
                ->where('id', '=', $reason_id)
                ->where('callcenter_id', '=', $callcenter_id)
                ->get()
                ->first()
                ->delete();
        }
    }
}
