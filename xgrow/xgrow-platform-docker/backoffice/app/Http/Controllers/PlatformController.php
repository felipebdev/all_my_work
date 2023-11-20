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
use \Rogierw\RwAcme\Api as AcmeApi;


class PlatformController extends Controller
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
        $templates = DB::table('templates')->where('platform', '=', 1)->get();

        $data["customers"] = $customers;
        $data["templates"] = $templates;
        $data["platform"] = new stdClass;

        $data["platform"]->restrict_ips = 0;
        $data["platform"]->customer_id = 0;
        $data["platform"]->template_id = 0;

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
        $request->request->add(['url' => 'https://' . str_replace(['http://', 'https://'], ['', ''], ($request->platform_url ?? ''))]);
        $request->request->add(['template_id' => $request->templates]);
        $request->request->add(['slug' => str_replace(['http://', 'https://'], ['', ''], ($request->slug ?? ''))]);
        $request->request->add(['recipient_id' => $request->recipient_id ?? '']);
        $request->request->add(['template_schema' => 1]);
        $restrict_ips = $request->restrict_ips ? 1 : 0;

        $request->request->add(['restrict_ips' => $restrict_ips]);
        $request->request->add(['ips_available' => $request->ips_available]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'unique:platforms'],
            'url' => ['required', 'string', 'unique:platforms'],
            'templates' => ['required'],
            'slug' => ['nullable', 'string', 'unique:platforms'],
        ]);

        $validator->validate();

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()]);
        }

        $client = $this->client->find($request->customer_id);

        if ($request->id == 0) {
            $uuid = Uuid::generate(4)->string;
            $request->request->add(['id' => $uuid]);

            $platform = $this->platform->create($request->all());

            $this->platformSiteConfig->create(['platform_id' => $uuid]);

            /*
            $this->createSchemaFolderSite($uuid);
            setApacheConfig($platform->name_slug, $platform->url);
            $this->fillFilesFromSite($uuid);
            $this->copyLayoutAssets($uuid);
            $this->createSiteConfFile($uuid);


            $emails = Email::all();
            foreach ($emails as $email) {
                $this->emailPlatform->create([
                    'message' => $email->message,
                    'from' => $email->from,
                    'email_id' => $email->id,
                    'platform_id' => $uuid
                ]);
            }


           setSslConfig($client, $platform->name_slug, $platform->url);
           $this->generateSsl($client, $platform->url);
           */

        } else {
            $this->platform->find($request->id)->update($request->all());
        }

        return redirect('/platforms');
    }

    public function renew($id)
    {

        $platform = $this->platform->find($id);


        if ($platform->template_schema == 2) {
            return back()->withErrors(['error' => 'Versão não disponível']);
        }

        $this->createSchemaFolderSite($id);
        $this->deleteFilesFromSite($id);
        $this->fillFilesFromSite($id);
        $this->copyLayoutAssets($id);
        $this->copyWelcomeAssets($id);

        $BASE_DIR = BASE_DIR . $platform->name_slug . SEPARATOR;

        $sections = $platform->sections;

        foreach ($sections as $section) {
            $path = $BASE_DIR . $section->name_slug;
            deleteFilesFromFolder($path);
            $this->fillFilesFromSection($section);
            $this->createSectionConfFile($section);
        }

        $platform->updated_at = date('Y-m-d G:i:s');
        $platform->save();

        //temp
        /*
        $config = $BASE_DIR . SEPARATOR . "config";
        createFolder($config);
        $this->createSiteConfFile($id);
        */
        //end temp

        return redirect('/platforms');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
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

        $templates = DB::table('templates')->where('platform', '=', 1)->get();

        $data["platform"] = $platform;
        $data["customers"] = $customers;
        $data["templates"] = $templates;

        return view('platforms.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $platform = Platform::find($id);

        $request->request->add(['slug' => str_replace(['http://', 'https://'], ['', ''], ($request->slug ?? ''))]);

        $validator = Validator::make($request->only('slug'), [
            'slug' => ['nullable', 'string', 'unique:platforms,slug,' . $id],
        ]);

        $validator->validate();

        $platform->name = $request->platform_name;
        $platform->url = 'https://' . str_replace(['http://', 'https://'], ['', ''], ($request->platform_url ?? ''));
        $platform->customer_id = $request->customer_id;
        $platform->slug = $request->slug;
        $platform->template_id = $request->templates;
        $platform->restrict_ips = $request->restrict_ips ? 1 : 0;
        $platform->ips_available = $request->ips_available;
        $platform->recipient_id = $request->recipient_id;

        if ($platform->featured_image !== null && $request->remove_image == true) {
            $this->removeImage($platform->featured_image);
            $platform->featured_image = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($platform->featured_image !== null) {
                $this->removeImage($platform->featured_image);
            }

            $image = $this->saveImage($request->featured_image);

            if ($image !== false) {
                $platform->featured_image = $image;
            }
        }

        $platform->save();

        return redirect('/platforms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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

    private function createSchemaFolderSite($id)
    {
        $platform = $this->platform->find($id);
        $BASE_DIR = BASE_DIR . $platform->name_slug;
        createFolder($BASE_DIR);

        $config = $BASE_DIR . SEPARATOR . "config";
        createFolder($config);

        $assets = $BASE_DIR . SEPARATOR . "assets";
        createFolder($assets);
        createFolder($assets . SEPARATOR . "js");
        createFolder($assets . SEPARATOR . "css");
        createFolder($assets . SEPARATOR . "images");
        createFolder($assets . SEPARATOR . "plugin");

        //Features
        createFolder($BASE_DIR . SEPARATOR . "cursos");
        createFolder($BASE_DIR . SEPARATOR . "forum");

        //plugin
        createFolder($assets . SEPARATOR . "plugin" . SEPARATOR . "summernote");

    }

    function fillFilesFromSite($id)
    {
        $platform = $this->platform->find($id);
        $TBP = TEMPLATES_BASE_PATH . SEPARATOR;
        $BASE_DIR = BASE_DIR . $platform->name_slug . SEPARATOR;

        copyFile($TBP . "index.html", $BASE_DIR);
        copyFile($TBP . "welcome.html", $BASE_DIR);
        copyFile($TBP . "terms.html", $BASE_DIR);
        copyFile($TBP . "edit-user.html", $BASE_DIR);
        copyFile($TBP . "live.html", $BASE_DIR);
        copyFile($TBP . "repasswd.html", $BASE_DIR);
        copyFile($TBP . "category.html", $BASE_DIR);
        copyFile($TBP . ".htaccess", $BASE_DIR);

        $origin_assets = $TBP . "assets" . SEPARATOR;
        $destiny_assets = $BASE_DIR . "assets" . SEPARATOR;

        fillFilesFromFolder($origin_assets . "js", $destiny_assets . "js");
        fillFilesFromFolder($origin_assets . "css", $destiny_assets . "css");
        fillFilesFromFolder($origin_assets . "images", $destiny_assets . "images");

        //copia arquivos das features
        fillFilesFromFolder($TBP . "cursos", $BASE_DIR . "cursos");
        fillFilesFromFolder($TBP . "forum", $BASE_DIR . "forum");

        recurse_copy($origin_assets . "plugin" . SEPARATOR . "summernote", $destiny_assets . "plugin" . SEPARATOR . "summernote");

    }

    function copyLayoutAssets($id)
    {
        $platform = $this->platform->find($id);
        $template_folder = TEMPLATES_BASE_PATH . SEPARATOR . $platform->template->folder . SEPARATOR;
        $asset = BASE_DIR . $platform->name_slug . SEPARATOR . "assets" . SEPARATOR;

        deleteFile($asset . "js" . SEPARATOR . "layout.js");
        deleteFile($asset . "css" . SEPARATOR . "layout.css");

        copyFile($template_folder . "layout.js", $asset . "js");
        copyFile($template_folder . "layout.css", $asset . "css");
    }

    function copyWelcomeAssets($id)
    {
        $platform = $this->platform->find($id);


        if ($platform->platformSiteConfig->welcome_template_id != null) {

            $template_folder = TEMPLATES_BASE_PATH . SEPARATOR . $platform->platformSiteConfig->template->folder . SEPARATOR;
            $asset = BASE_DIR . $platform->name_slug . SEPARATOR . "assets" . SEPARATOR;

            deleteFile($asset . "js" . SEPARATOR . "welcome.js");
            deleteFile($asset . "css" . SEPARATOR . "welcome.css");

            copyFile($template_folder . "welcome.js", $asset . "js");
            copyFile($template_folder . "welcome.css", $asset . "css");

        }

    }

    private function deleteFilesFromSite($id)
    {
        $platform = $this->platform->find($id);

        $BASE_DIR = BASE_DIR . $platform->name_slug . SEPARATOR;

        $path_assets = $BASE_DIR . "assets";

        deleteFilesFromFolder($path_assets . SEPARATOR . "js");
        deleteFilesFromFolder($path_assets . SEPARATOR . "css");
        deleteFilesFromFolder($path_assets . SEPARATOR . "images");

        $welcome = $BASE_DIR . 'welcome.html';
        $welcome = $BASE_DIR . 'terms.html';
        $index = $BASE_DIR . 'index.html';
        $edit_user = $BASE_DIR . 'edit-user.html';
        $live = $BASE_DIR . 'live.html';
        $repasswd = $BASE_DIR . 'repasswd.html';
        $category = $BASE_DIR . 'category.html';
        $htaccess = $BASE_DIR . '.htaccess';

        deleteMultFiles([$welcome, $index, $edit_user, $live, $repasswd, $category, $htaccess]);

        //Exclui features
        deleteFilesFromFolder($BASE_DIR . "cursos");
        deleteFilesFromFolder($BASE_DIR . "forum");

    }

    private function createSiteConfFile($id)
    {

        $platform = $this->platform->find($id);
        $url_platform = config('app.url_platform');
        $env = config('app.env');

        $content = "platform = { \n";
        $content .= "  'id': '$platform->id',\n";
        $content .= "  'name': '{$platform->name}',\n";
        $content .= "  'url_web': '{$url_platform}',\n";
        $content .= "  'mode': '{$env}',\n";
        $content .= " } \n";

        $path = BASE_DIR . $platform->name_slug . SEPARATOR . 'config';

        createFile("platform.js", $content, $path);
    }

    private function fillFilesFromSection($section)
    {
        $template = $this->template->find($section->template_id);
        $platform = $section->platform;
        $source = TEMPLATES_BASE_PATH . SEPARATOR . $template->folder;
        $destiny = BASE_DIR . $platform->name_slug . SEPARATOR . $section->name_slug;
        fillFilesFromFolder($source, $destiny);
    }

    private function createSectionConfFile($section)
    {
        $content = "section = { \n";
        $content .= "  'section_key': '{$section->section_key}',\n";
        $content .= "  'name': '{$section->name}',\n";
        $content .= "  'name_slug': '{$section->name_slug}',\n";
        $content .= " } \n";

        $path = BASE_DIR . $section->platform->name_slug . SEPARATOR . $section->name_slug;

        createFile("section.js", $content, $path);
    }

    public function acme(Request $request)
    {

        $url = 'https://teste1.fandone.com.br';

        $client = $this->client->find($request->customer_id);

        $this->generateSsl($client, $url);
//        setSslConfig($client, $platformUrl);
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

    //método temporário
    public function testeCreateFolder()
    {

        $BASE_DIR = BASE_DIR . 'create-folder-' . time();
        createFolder($BASE_DIR);
    }

    public function saveImage($image)
    {
        if (!$this->checkExtension($image->extension())) {
            return false;
        }

        if (!$this->checkSize($image->getSize())) {
            return false;
        }

        $filename = $this->createFileName($image->getClientOriginalName(), $image->extension());
        $image->move(public_path("uploads/platforms/"), $filename);

        return url("uploads/platforms/{$filename}");
    }

    public function checkExtension($extension)
    {
        if ($extension != "png" && $extension != "jpg" && $extension != "jpeg") {
            return false;
        }

        return true;
    }

    public function checkSize($size)
    {
        $size = $size / 1000; // Pega o tamanho em MB

        if ($size > 2048) {
            return false;
        }

        return true;
    }

    public function createFileName($originalname, $extension)
    {
        return hash("md5", $originalname . time()) . "." . $extension;
    }

    public function removeImage($filename)
    {
        $filename = explode('/', $filename);
        $filename = end($filename);
        $filepath = public_path("/uploads/platforms/{$filename}");
        unlink($filepath);
    }
}
