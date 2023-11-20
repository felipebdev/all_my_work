<?php

namespace App\Http\Traits;

use App\Observers\ElasticsearchObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

trait ElasticsearchTrait
{
    /**
     * @return void
     */
    public static function bootElasticsearchTrait()
    {
        if (Config::get('audit.enabled')) {
            static::observe(ElasticsearchObserver::class);
        }
    }

    /**
     * @return string
     */
    public function getSearchIndex(): string
    {
        return $this->getTable();
    }

    /**
     * @return mixed
     */
    public function getSearchType()
    {
        if (property_exists($this, 'useSearchType')) {
            return $this->useSearchType;
        }

        return $this->getTable();
    }

    /**
     * @return array
     */
    public function toSearchArray(): array
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function toSearchJson(): string
    {
        return json_encode($this->toArray());
//        return $this->to
    }

    /**
     * Get extra data
     * @return array
     */
    public function getExtraData(): array
    {
        return [
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name ?? '',
            'user_email' => Auth::user()->email ?? '',
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'url' => Request::fullUrl(),
        ];
    }
}
