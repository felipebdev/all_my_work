<?php

namespace App\Http\Controllers;

use App\CallcenterRestrictedIp;

class CallCenterRestrictedIpController extends Controller
{
    public function __construct()
    {
        $this->restrictedIp = new CallcenterRestrictedIp;
    }

    public function index($callcenter_id)
    {
        
        return $this->restrictedIp->where('callcenter_id', '=', $callcenter_id)->get();
    }

    public function store($ips, $callcenter_id)
    {
        foreach ($ips as $ip) {
            if (!$this->checkIfExists($ip, $callcenter_id)) {
                $this->restrictedIp->create([
                    'callcenter_id' => $callcenter_id,
                    'ip_address' => $ip,
                ]);
            }
        }
    }

    public function checkIfExists($ip_address, $callcenter_id)
    {
        $exists = $this->restrictedIp
            ->where('callcenter_id', '=', $callcenter_id)
            ->where('ip_address', '=', $ip_address)
            ->exists();
        
        if ($exists) {
            return true;
        }

        return false;
    }

    public function update($ips, $callcenter_id)
    {
        foreach ($ips as $id => $address) {
            $ip = $this->restrictedIp
                ->where('id', '=', $id)
                ->where('callcenter_id', '=', $callcenter_id)
                ->get()
                ->first();
            $ip->ip_address = $address;
            $ip->save();
        }
    }

    public function delete($ips, $callcenter_id)
    {
        foreach ($ips as $ip_id) {
            $this->restrictedIp
                ->where('id', '=', $ip_id)
                ->where('callcenter_id', '=', $callcenter_id)
                ->get()
                ->first()
                ->delete();
        }
    }
}
