<?php

namespace App\Http\Controllers;

use App\Client;
use App\Facades\AccessLogFacade;
use App\File;
use App\Http\Requests\StorePlatformSessionRequest;
use App\Platform;
use App\PlatformUser;
use App\Services\Auth\ClientStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class PlatformController extends Controller
{

    public function index()
    {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        if (env('APP_ENV') !== 'production') {
            $permissionToCreatePlatform = $status->isClient;
        } else {
            $permissionToCreatePlatform = Client::isUserAClient();
        }

        return view('platforms.index', [
            'platforms' => Auth::user()->platforms,
            'permissionToCreatePlatform' => $permissionToCreatePlatform,
            'isClient' => $status->isClient,
            'clientApproved' => $status->clientApproved,
            'recipientStatusMessage' => $status->recipientStatusMessage,
            'verifyDocument' => $status->mustVerify
        ]);
    }


    public function choose(StorePlatformSessionRequest $request)
    {

        $request->session()->forget('platform_id');

        $request->session()->put('platform_id', $request->platform);

        $request->session()->save();

        AccessLogFacade::build(Auth::user(), $request->platform)
            ->logChoosedPlatform();

        $platformId = $request->session()->get('platform_id', false);
        if (!$platformId) {
            return redirect()->route('choose.platform');
        }

        return redirect()->route($request->redirect ?? 'home');
    }

    /** New functions Single Login */
    public function create()
    {
        $platform = new Platform();
        return view('platforms.new-platform', ['platform' => $platform]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'unique:platforms'],
            'url' => ['required', 'string', 'unique:platforms'],
            'slug' => ['nullable', 'string', 'unique:platforms'],
        ]);
        $validator->validate();
        if ($validator->fails()) return redirect()->back()->with('error', $validator->errors()->first());

        try {
            $client = Client::where('email', Auth::user()->email)->first();
            if ($client) {
                $thumb = File::setUploadedFile($request, 'thumb');
                $uuid = Uuid::uuid4();

                Platform::insert([
                    'id' => $uuid,
                    'name' => $request->input('name'),
                    'url' => $request->input('url'),
                    'name_slug' => Str::slug($request->input('name'), '-'),
                    'slug' => $request->input('slug'),
                    'template_id' => 1,
                    'customer_id' => $client->id,
                    'created_at' => Carbon::now()
                ]);

                $platform = Platform::where('id', $uuid)->first();

                File::saveUploadedFile($platform, $thumb, 'thumb_id');

                DB::table('platform_user')->insert([
                    'platform_id' => $uuid,
                    'platforms_users_id' => Auth::id()
                ]);
                return redirect('platforms')->with('success', 'Plataforma criada com sucesso.');
            } else {
                return redirect('platforms')->with('error', 'Você não tem permissão de criar uma plataforma.');
            }
        } catch (Exception $e) {
            Log::error('Ocorreu um erro ao criar nova plataforma. Error: ' . json_encode($request) . ' | Exception: ' . $e->getMessage());
        }
    }

    public function acceptTerms()
    {
        try {
            $user = PlatformUser::find(Auth::user()->id);
            $user->accepted_terms = true;
            $user->save();
            return redirect('platforms')->with('success', 'Termos aceitos.');
        } catch (Exception $e) {
            Log::error('Ocorreu um erro. Error: ' . $e->getMessage());
        }
    }
}
