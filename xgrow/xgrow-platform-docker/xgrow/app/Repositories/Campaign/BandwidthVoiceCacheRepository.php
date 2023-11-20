<?php

namespace App\Repositories\Campaign;

use Illuminate\Support\Facades\Redis;

class BandwidthVoiceCacheRepository
{

    private $baseKey = 'bandwidth:voice-callback:';

    private function generateKeyFromTag(string $tag): string
    {
        return $this->baseKey.$tag;
    }

    /**
     * Store call info on Redis
     *
     * @param  string  $tag
     * @param  string  $url
     * @param  string  $number Telephone number
     */
    public function storeCallInfo(string $tag, string $url, string $number)
    {
        $key = $this->generateKeyFromTag($tag);
        Redis::hMSet($key, [
            'isSent' => 0,
            'tries' => 0,
            'url' => $url,
            'number' => $number
        ]);
        Redis::expire($key, 60*60*24*7);
    }

    /**
     * Increment Call tries
     *
     * @param  string  $key
     */
    public function incrementCallTries(string $tag)
    {
        $key = $this->generateKeyFromTag($tag);
        Redis::hIncrBy($key, 'tries', 1);
    }

    /**
     * Iterator for items from groupId
     *
     * Prefix is removed, data is in $tag => $value format
     *
     * eg:
     * while($cache->iterateByGroupUuid('0123') as $tag => $value) {
     *   // do things
     * }
     *
     * @param  string  $groupUuid
     * @return \Generator
     */
    public function iterateByGroupUuid(string $groupUuid)
    {
        $pattern = "{$this->baseKey}{$groupUuid}:*";

        $cursor = '0';
        do {
            [$cursor, $keys] = Redis::scan($cursor, 'match', $pattern);
            foreach ($keys as $key) {
                $tag = preg_replace("/^{$this->baseKey}/", '', $key);
                yield $tag => Redis::hgetall($key);
            }
        } while ($cursor);
    }

    /**
     * Get data from tag
     *
     * @param  string  $tag
     * @return mixed
     */
    public function get(string $tag)
    {
        $key = $this->generateKeyFromTag($tag);
        return Redis::hgetall($key);
    }

    /**
     * Mark tag as sent
     *
     * @param  string  $tag
     */
    public function markAsSent(string $tag)
    {
        $key = $this->generateKeyFromTag($tag);
        Redis::hSet($key, 'isSent', 1);
    }

}
