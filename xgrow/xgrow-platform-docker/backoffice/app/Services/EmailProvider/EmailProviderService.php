<?php

namespace App\Services\EmailProvider;

use App\EmailProvider;
use App\Repositories\EmailProviderRepository;
use App\Services\Objects\EmailProviderFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\CacheEntry;
use Illuminate\Support\Facades\Cache;


class EmailProviderService
{
    protected EmailProviderRepository $emailProvider;

    public function __construct(EmailProviderRepository $emailProvider)
    {
        $this->emailProvider = $emailProvider;
    }

    /**
     * @param array $input
     * @return Builder[]|Collection
     */
    public function getEmailProviders(array $input)
    {
        $search = $input['search'] ?? null;

        $emailProviderFilter = (new EmailProviderFilter())
                                    ->setSearch($search);

        return $this->emailProvider->listAll($emailProviderFilter)
                                    ->get();

    }

    /**
     * Get email provider data
     * @param int $id
     * @return Model
     */
    public function getEmailProvider(int $id): Model
    {
        return $this->emailProvider->findById($id);
    }



    /**
     * Set email provider cache
     * @param string $search
     * @return void
     */
    public function setEmailProviderCache(string $search):void{

        $emailProviderFilter = (new EmailProviderFilter())
                                    ->setSearch($search);


        $provider = $this->emailProvider->listAll($emailProviderFilter)
                                    ->firstOrFail();


        $entry = CacheEntry::where('name', '=', 'MAIL_PROVIDER_NAME')->firstOrFail();
        $entry->default_value = $provider->name;
        $entry->save();

        Cache::driver('redis')->setPrefix(env('REDIS_PREFIX_APP'));
        Cache::driver('redis')->forget('MAIL_PROVIDER_NAME');
    }

    /**
     * @param array $inputs
     * @return EmailProvider
     */
    public function create(array $inputs): EmailProvider
    {
        $inputs['service_tags'] = $this->convTag($inputs['service_tags']);
        return $this->emailProvider->create($inputs);
    }

    /**
     * @param $id
     * @param array $inputs
     * @return mixed
     */
    public function update($id, array $inputs)
    {
        $inputs['service_tags'] = $this->convTag($inputs['service_tags']);;
        return $this->emailProvider->update($id, $inputs);
    }

    /**
     * @param $service_tags
     * @return false|string
     */
    private function convTag($service_tags)
    {
        return json_encode(
            array_map('trim', explode(',', $service_tags ?? ''))
        );
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id)
    {
        $this->emailProvider->delete($id);
    }

}

?>
