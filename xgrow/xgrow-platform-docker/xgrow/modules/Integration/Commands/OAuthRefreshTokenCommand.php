<?php

namespace Modules\Integration\Commands;

use App\Logs\XgrowLog;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Integration\Contracts\IAppIntegrationRepository;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Models\Integration;

class OAuthRefreshTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "xgrow:apps:oauth-refresh
                            {codes : Comma separated integration code (Ex:'1,2,3')}
                            {--platform : Platform ID}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update oauth integration\'s tokens';

    /**
     * @var Modules\Integration\Contracts\IAppIntegrationRepository
     */
    private $repository;

    public function __construct(IAppIntegrationRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function handle()
    {
        try {
            $platformId = $this->option('platform');
            $codes = $this->argument('codes');
            $codes = explode(',', $codes);
            $this->validateInputCodes($codes);

            $integrations = Integration::whereIn('apps.code', $codes)
                ->when($platformId, function ($query, $platformId) {
                    $query->where('apps.platform_id', '=', $platformId);
                })
                ->get();
            
            XgrowLog::xInfo(
                '🏳 Oauth refresh command started',
                ['total' => $integrations->count()],
                'integration'
            );

            foreach ($integrations as $element) {
                try {
                    $providerName = Str::ucfirst($element->type);
                    $providerClass =  "\Modules\Integration\Providers\\{$providerName}";
                    $provider = new $providerClass();
                    $token = $provider->refreshToken(
                        $element->api_key,
                        $element->api_secret,
                        $element->metadata['expires_in']
                    );
    
                    $updateData = [
                        'api_key' => $token->getAccessToken(),
                        'api_secret' => $token->getRefreshToken(),
                        'metadata' => ['expires_in' => $token->getExpiresIn()]
                    ];
    
                    $this->repository->baseUpdate($element->id, $updateData);

                    XgrowLog::xInfo(
                        '✔ Refresh token successful',
                        ['app_id' => $element->id, 'provider' => $element->type],
                        'integration'
                    );
                } catch (Exception $e) {
                    XgrowLog::xError(
                        '✖ Refresh token error',
                        $e,
                        ['app_id' => $element->id, 'provider' => $element->type],
                        'integration'
                    );
                }
            }

            XgrowLog::xInfo(
                '🏁 Oauth refresh command finished',
                [],
                'integration'
            );  
        } catch (Exception $e) {
            XgrowLog::xError(
                '✖ Oauth command error',
                $e,
                [],
                'integration'
            );
        }
    }

    private function validateInputCodes(array $codes)
    {
        $acceptedCodes = CodeEnum::getAllValues();
        foreach ($codes as $code) {
            if (!in_array($code, $acceptedCodes)) {
                throw new Exception("Code {$code} option is invalid");
            }
        }
    }
}
