<?php

namespace App\Helpers;

use App\AccessLog;
use App\Platform;
use Exception;
use Illuminate\Support\Facades\Log;

class AccessLogHelper 
{   
    private $user;
    private $platformId;

    public function build($user, $platformId = ''): self {
        $this->user = $user;
        $this->platformId = $platformId;

        return $this;
    }

    public function logSuccessfulLogin(): void {
        if (empty($this->user)) return;

        try {
            AccessLog::create([
                'user_id' => $this->user->id,
                'user_type' => $this->user->getTable(),
                'type' => 'LOGIN',
                'description' => 'Usuário ' . $this->user->email . ' efetuou login no sistema',
                'platform_id' => $this->user->platform_id ?? '',
                'ip' => $_SERVER["REMOTE_ADDR"],
                'browser_type' => AccessLog::searchBrowser($_SERVER['HTTP_USER_AGENT']),
                'device_type' => AccessLog::searchDevice($_SERVER['HTTP_USER_AGENT'])
            ]);
        }
        catch(Exception $e) {
            Log::error('Can not log successful login > ', ['error' => $e->getMessage()]);
        }
    }

    public function logChoosedPlatform(): void {
        if (empty($this->user)) return;
        
        try {
            $platform = Platform::findOrFail($this->platformId);
            AccessLog::create([
                'user_id' => $this->user->id,
                'user_type' => $this->user->getTable(),
                'type' => 'LOGIN',
                'description' => "Usuário {$this->user->email} escolheu a plataforma {$platform->name}",
                'platform_id' => $platform->id ?? '',
                'ip' => $_SERVER["REMOTE_ADDR"],
                'browser_type' => AccessLog::searchBrowser($_SERVER['HTTP_USER_AGENT']),
                'device_type' => AccessLog::searchDevice($_SERVER['HTTP_USER_AGENT'])
            ]);
        }
        catch(Exception $e) {
            Log::error('Can not log choosed platform > ', ['error' => $e->getMessage()]);
        }
    }

    public function logSuccessfulLogout(): void {
        if (empty($this->user)) return;

        try {
            AccessLog::create([
                'user_id' => $this->user->id,
                'user_type' => $this->user->getTable(),
                'type' => 'LOGIN',
                'description' => 'Usuário ' . $this->user->email . ' saiu do sistema',
                'platform_id' => $this->user->platform_id ?? '',
                'ip' => $_SERVER["REMOTE_ADDR"],
                'browser_type' => AccessLog::searchBrowser($_SERVER['HTTP_USER_AGENT']),
                'device_type' => AccessLog::searchDevice($_SERVER['HTTP_USER_AGENT'])
            ]);
        }
        catch(Exception $e) {
            Log::error('Can not log successful logout > ', ['error' => $e->getMessage()]);
        }
    }

}