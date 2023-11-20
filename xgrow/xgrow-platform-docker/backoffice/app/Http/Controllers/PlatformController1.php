<?php

namespace App\Http\Controllers;

use StdClass;
use App\Email;
use App\Client;
use App\EmailPlatform;
use App\message;
use App\Platform;
use App\PlatformSiteConfig;
use App\PlatformUser;
use App\Template;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;


class PlatformController1 extends Controller
{
    private $platform;
    private $platformSiteConfig;
    private $template;
    private $client;
    private $emailPlatform;

    public function __construct(Platform $platform, PlatformSiteConfig $platformSiteConfig, Template $template, Client $client, EmailPlatform $emailPlatform)
    {
        $this->platform = $platform;
        $this->platformSiteConfig = $platformSiteConfig;
        $this->template = $template;
        $this->client = $client;
        $this->emailPlatform = $emailPlatform;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];

        $platforms = Platform::select('platforms.*', DB::raw('CONCAT(first_name," ",last_name) as customer_name'))
            ->join('clients', 'platforms.customer_id', '=', 'clients.id')->orderBy('updated_at', 'DESC')->get();

        $data["platforms"] = $platforms;

        return view('platforms.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];

        $customers = DB::table('clients')->get();
        $templates = DB::table('templates')->where('platform','=',1)->get();

        $data["customers"] = $customers;
        $data["templates"] = $templates;
        $data["platform"] = new stdClass;

        $data["platform"]->restrict_ips = 0;
        $data["platform"]->customer_id  = 0;
        $data["platform"]->template_id  = 0;

        return view('platforms.create', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $request->request->add(['name' => $request->platform_name]);
        $request->request->add(['url' => $request->platform_url]);
        $request->request->add(['template_id'=>$request->templates]);
        $request->request->add(['template_schema' => 2]);

        $restrict_ips = $request->restrict_ips ? 1 : 0;

        $request->request->add(['restrict_ips' => $restrict_ips]);
        $request->request->add(['ips_available' => $request->ips_available]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'unique:platforms'],
            'url' => ['required', 'string', 'unique:platforms'],
            'templates' => ['required'],
        ]);

        $validator->validate();

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()]);
        }

        //$client = $this->client->find($request->customer_id);

        if($request->id == 0 ) {
            $uuid = Uuid::generate(4)->string;
            $request->request->add(['id' => $uuid]);

            $platform = $this->platform->create($request->all());
            $platform->id = $uuid;

            $this->platformSiteConfig->create(['platform_id' => $uuid]);
            $this->createSchemaFolderSite($uuid);
            setApacheConfig($platform->name_slug, $platform->url);

            $this->renewTemplate($platform);

            $emails = Email::all();
            foreach ($emails as $email) {
                $this->emailPlatform->create([
                    'message' => $email->message,
                    'from' => $email->from,
                    'email_id' => $email->id,
                    'platform_id' => $uuid
                ]);
            }

//            setSslConfig($client, $platform->name_slug, $platform->url);
//            $this->generateSsl($client, $platform->url);

        } else {
            $this->platform->find($request->id)->update($request->all());
        }

        return redirect('/platforms');
    }

    public function renew($id)
    {

        $platform = $this->platform->find($id);

        if($platform->template_schema == 2){
            $this->renewTemplate($platform);
        }
        else{
             return back()->withErrors(['error' => 'Modelo de template desatualizado']);
        }

        $platform->updated_at = date('Y-m-d G:i:s');
        $platform->save();

        return redirect('/platforms');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [];

        $platform = Platform::find($id);

        if (!$platform) {
            return redirect('/platforms');
        }

        $customers = DB::table('clients')->get();

        $templates = DB::table('templates')->where('platform','=',1)->get();

        $data["platform"] = $platform;
        $data["customers"] = $customers;
        $data["templates"] = $templates;

        return view('platforms.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $platform = Platform::find($id);

        $platform->name = $request->platform_name;
        $platform->url = $request->platform_url;
        $platform->customer_id = $request->customer_id;
        $platform->template_id = $request->templates;
        $platform->restrict_ips = $request->restrict_ips ? 1 : 0;
        $platform->ips_available = $request->ips_available;
        $platform->save();

        return redirect('/platforms');
    }

    private function renewTemplate($platform){
       $count = $this->replaceDataConfig($platform);

       if (!$count) {
            return back()->withErrors(['error' => 'Erro de configuração']);
        }
       else{
            //exclui pasta dist da comunidade
           $base_dir = BASE_DIR . $platform->name_slug . SEPARATOR;


           $folder_dist =  $base_dir . "dist";
           if(!is_dir($folder_dist)) createFolder($folder_dist);

           $folder_stats =  $base_dir . "static";
           if(!is_dir($folder_stats)) createFolder($folder_stats);

           //atualiza pasta dist da comunidade
           $template_dir = TEMPLATES_BASE_PATH_VUE . SEPARATOR;
           $template_dist = $template_dir . "dist";
           recurse_copy($template_dist, $folder_dist);

           //copia página inicial
           deleteFile($base_dir . "index.html");
           copyFile($template_dir . "index.html", $base_dir);
       }

    }

    private function replaceDataConfig($platform){
        $file = TEMPLATES_BASE_PATH_VUE . SEPARATOR . "dist" . SEPARATOR . 'build.js';

        $original_content = file_get_contents($file);

        $pattern = "'id': '([^']*)', 'url_web': '([^']*)'";

        $url_web = config('app.url_platform');
        
        $changed_content = preg_replace("/{$pattern}/i", "'id': '{$platform->id}', 'url_web': '{$url_web}'", $original_content, 1, $count);

        file_put_contents($file, $changed_content);

        //total de modificações
        return $count;
    }

    //Fim atualização template vue

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = PlatformUser::where('platform_id', $id)->get();

        if ($users->count()) {
            return back()->withErrors(['user_exists' => 'Esta plataforma possui usuários cadastrados!']);
        }

        $platform = Platform::find($id);

        $platform->delete();

        return redirect('/platforms');
    }

    private function createSchemaFolderSite($id){
        $platform = $this->platform->find($id);
        $BASE_DIR = BASE_DIR . $platform->name_slug;
        createFolder($BASE_DIR);
    }

    public function generateSsl($customer, $url)
    {
        $domain = str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $url)));

        $path = 'C:\\teste-folder\\brasil\\';

        $client = new AcmeApi($customer->email, $path . '__account');

        $account = (!$client->account()->exists()) ? $client->account()->create() : $client->account()->get();

        $order = $client->order()->new($account, [$domain]);
        $order = $client->order()->get($order->id);

        $validationStatus = $client->domainValidation()->status($order);
        $validationData = $client->domainValidation()->getFileValidationData($validationStatus);

        try {
            $client->domainValidation()->start($account, $validationStatus[0]);
        } catch (DomainValidationException $exception) {
            dd($exception->getMessage());
        }

        $privateKey = \Rogierw\RwAcme\Support\OpenSsl::generatePrivateKey();
        $csr = \Rogierw\RwAcme\Support\OpenSsl::generateCsr([$domain], $privateKey);

        if ($order->isFinalized()) {
            $certificateBundle = $client->certificate()->getBundle($order);
        }
    }


}

