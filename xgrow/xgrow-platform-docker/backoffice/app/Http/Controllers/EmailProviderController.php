<?php

namespace App\Http\Controllers;

use App\CacheEntry;
use App\EmailProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmailProviderController extends Controller
{

    public function index()
    {
        $providers = EmailProvider::paginate(10);
        foreach ($providers as $provider) {
            $provider->service_tags = implode(', ', json_decode($provider['service_tags'] ?? '[]'));
        }

        return view('email-provider.index')
            ->with('defaultProvider', CacheEntry::where('name', '=', 'MAIL_PROVIDER_NAME')->first()->default_value ?? null)
            ->with('cachedProvider', Cache::driver('redis')->get('MAIL_PROVIDER_NAME'))
            ->with('providers', $providers);
    }

    public function create()
    {
        return view('email-provider.create')
            ->with('data', ['type' => 'create'])
            ->with('provider', new EmailProvider())
            ->with('drivers', EmailProvider::DRIVERS);
    }

    public function store(Request $request)
    {
        if (!$this->isJsonValid($request->settings)) {
            return back()->withErrors('JSON inválido');
        }

        $tagList = array_map('trim', explode(',', $request->service_tags ?? ''));

        $email = new EmailProvider($request->only([
            'name', 'description', 'from_name', 'from_address', 'driver', 'settings'
        ]));
        $email->service_tags = json_encode($tagList);
        $email->save();

        return redirect()->route('email-provider.index');
    }

    public function edit(EmailProvider $provider)
    {
        $provider->service_tags = implode(', ', json_decode($provider['service_tags'] ?? '[]'));
        $provider->settings = isset($provider['settings'])
            ? json_encode(json_decode($provider['settings']), JSON_PRETTY_PRINT)
            : '';

        return view('email-provider.edit')
            ->with('data', ['type' => 'edit'])
            ->with('provider', $provider)
            ->with('drivers', EmailProvider::DRIVERS);
    }

    public function update(Request $request, EmailProvider $provider)
    {
        if (!$this->isJsonValid($request->settings)) {
            return back()->withErrors('JSON inválido');
        }

        $tagList = array_map('trim', explode(',', $request->service_tags ?? ''));

        $provider->fill($request->only([
            'name', 'description', 'from_name', 'from_address', 'driver', 'settings'
        ]));
        $provider->service_tags = json_encode($tagList);
        $provider->save();

        return redirect()->route('email-provider.index');
    }

    public function destroy(EmailProvider $provider)
    {
        $provider->delete();

        return redirect()->route('email-provider.index');
    }

    public function apply(Request $request)
    {
        $provider = EmailProvider::where('name', '=', $request->provider)->firstOrFail();
        $entry = CacheEntry::where('name', '=', 'MAIL_PROVIDER_NAME')->firstOrFail();
        $entry->default_value = $provider->name;
        $entry->save();

        Cache::driver('redis')->setPrefix(env('REDIS_PREFIX_APP'));
        Cache::driver('redis')->forget('MAIL_PROVIDER_NAME');

        return redirect()->route('email-provider.index');
    }

    private function isJsonValid($jsonString): bool
    {
        return !(json_decode($jsonString) === null);
    }
}
