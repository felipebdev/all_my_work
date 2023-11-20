<?php

namespace App\Http\Controllers;

use App\Client;
use App\Content;
use App\Course;
use App\File;
use App\Helpers\SecurityHelper;
use App\Menu;
use App\Permission;
use App\Platform;
use App\PlatformSiteConfig;
use App\PlatformUser;
use App\PlatformUserAccess;
use App\Section;
use App\Template;
use App\Widget;
use App\Helpers\CollectionHelper;
use App\Http\Traits\CustomResponseTrait;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlatformSiteConfigController extends Controller
{
    use CustomResponseTrait;
    private $platformSiteConfig;
    private $template;
    private $platform;
    private $menu;
    private $section;
    private $course;
    private $content;
    private $widget;
    private $platformUser;
    private $platformUserAccess;

    public function __construct(
        PlatformSiteConfig $platformSiteConfig,
        Template $template,
        Platform $platform,
        Menu $menu,
        Section $section,
        Course $course,
        Content $content,
        Widget $widget,
        PlatformUser $platformUser,
        PlatformUserAccess $platformUserAccess
    ) {
        $this->platformSiteConfig = $platformSiteConfig;
        $this->template = $template;
        $this->platform = $platform;
        $this->menu = $menu;
        $this->section = $section;
        $this->course = $course;
        $this->content = $content;
        $this->widget = $widget;
        $this->platformUser = $platformUser;
        $this->platformUserAccess = $platformUserAccess;
    }


    public function platformProfileEdit()
    {
        $config = $this->platform::where('id', '=', Auth::user()->platform_id)->get();
        $client = Client::where('id', $config[0]->customer_id)->first();
        return view('platform-profile.edit', compact('config', 'client'));
    }

    public function platformProfileStore(Request $request)
    {

        $active = (isset($request->active)) ? 1 : 0;

        $request->request->add(['url_official' => $request->url_official]);

        $rules = [
            'url_official' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        $urlOfficialUnique = $this->platform->where('url_official', $request->url_official)->where('id', '<>', Auth::user()->platform_id)->count();

        $validator->validate();

        $errors = [];
        $fails = false;

        if ($urlOfficialUnique > 0) {
            $fails = true;
            $errors['url_official'] = "O campo Endereço Oficial já existe";
        }

        //Salva dados de venda
        $platform = Platform::where('id', Auth::user()->platform_id)->first();
        $request->merge([
            'active_sales' => $request->get("active_sales") == "t" ? 1 : 0,
        ]);
        $platform->reply_to_email = $request->reply_to_email;
        $platform->reply_to_name = $request->reply_to_name;
        $platform->url_official = $request->url_official;
        $platform->save();

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()]);
        } else if ($fails) {
            return redirect()->route('platform-profile.edit')->withErrors($errors);
        } else {
            $this->platform->find(Auth::user()->platform_id)->update($request->all());
            return redirect()->route('platform-profile.edit')->with(['message' => 'Dados alterados com sucesso!']);
        }

        //        $validator = Validator::make($request->all(), [
        //            'name' => ['required', 'string', 'unique:platforms'],
        //            'url' => ['required', 'string', 'unique:platforms']
        //        ]);
        //
        //        $validator->validate();
        //
        //        if ($validator->fails()) {
        //            return response()->json(['error' => true, 'message' => $validator->errors()]);
        //        }else{
        //            $this->platform->find(Auth::user()->platform_id)->update($request->all());
        //        }
    }


    public function UserIndex()
    {
        $users = $this->platformUser
            ->select(
                'platforms_users.id',
                'platforms_users.name',
                'platforms_users.email',
                'platform_user.type_access',
                'permissions.name as permission'
            )
            ->leftJoin('platform_user', 'platform_user.platforms_users_id', '=', 'platforms_users.id')
            ->leftJoin('permissions', 'platform_user.permission_id', '=', 'permissions.id')
            ->where('platform_user.platform_id', Auth::user()->platform_id)
            ->get();

        $owner = $this->platformUser
            ->select(
                'platforms_users.email',
            )
            ->join('clients', 'platforms_users.email', '=', 'clients.email')
            ->join('platforms', 'platforms.customer_id', '=', 'clients.id')
            ->where('platforms.id', Auth::user()->platform_id)
            ->first();

        return view('usersPlatform.index', compact('users', 'owner'));
    }
    public function UserIndexNext()
    {
        $users = $this->platformUser
            ->select(
                'platforms_users.id',
                'platforms_users.name',
                'platforms_users.email',
                'platform_user.type_access',
                'permissions.name as permission'
            )
            ->leftJoin('platform_user', 'platform_user.platforms_users_id', '=', 'platforms_users.id')
            ->leftJoin('permissions', 'platform_user.permission_id', '=', 'permissions.id')
            ->where('platform_user.platform_id', Auth::user()->platform_id)
            ->get();

        $owner = $this->platformUser
            ->select(
                'platforms_users.email',
            )
            ->leftJoin('platform_user', 'platform_user.platforms_users_id', '=', 'platforms_users.id')
            ->join('clients', 'platforms_users.email', '=', 'clients.email')
            ->where('platform_user.platform_id', Auth::user()->platform_id)
            ->first();

        return view('usersPlatform.index-next', compact('users', 'owner'));
    }

    public function GetUsers(Request $request): JsonResponse
    {
        $offset = $request->input('offset') ?? 25;
        try {
            $users = $this->platformUser
                ->select(
                    'platforms_users.id',
                    'platforms_users.name',
                    'platforms_users.email',
                    'platform_user.type_access',
                    'permissions.name as permission'
                )
                ->leftJoin('platform_user', 'platform_user.platforms_users_id', '=', 'platforms_users.id')
                ->leftJoin('permissions', 'platform_user.permission_id', '=', 'permissions.id')
                ->where('platform_user.platform_id', Auth::user()->platform_id)
                ->get();

            $owner = $this->platformUser
                ->select(
                    'platforms_users.id',
                )
                ->leftJoin('platform_user', 'platform_user.platforms_users_id', '=', 'platforms_users.id')
                ->join('clients', 'platforms_users.email', '=', 'clients.email')
                ->where('platform_user.platform_id', Auth::user()->platform_id)
                ->first();

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'users' => CollectionHelper::paginate($users, $offset),
                    'owner' => $owner
                ]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function UserCreate()
    {
        $user = new PlatformUser();
        $user->active = 1;
        $user->type_access = 'restrict';
        $user->total_in_other_platforms = 0;
        $permission_id = 0;
        $permissions = Permission::wherePlatformId(Auth::user()->platform_id)->get()->pluck('name', 'id');
        return view('usersPlatform.create', compact('user', 'permission_id', 'permissions'));
    }

    public function UserVerify(Request $request)
    {
        try {
            $user = $this->checkUserExist($request->email);

            if (!$user)
                return response()->json([], 204);

            $platform_id = auth::user()->platform_id;

            $user->type_access = 'restrict';
            $user->permission_id = null;

            $access = $user->platform_access()->wherePlatformId($platform_id)->first();

            if ($access) {
                $user->type_access = $access->type_access;
                $user->permission_id = $user->type_access == 'restrict' ?
                    $access->permission_id :
                    null;
            }

            return response()->json(['response' => 'success', 'user' => $user]);
        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    public function UserStore(Request $request)
    {
        try {
            $user = $this->checkUserExist($request->email);
            if (!$user) {
                $this->validate($request, [
                    'email' => 'required|email',
                    'name' => 'required|string',
                    'password' => 'required'
                ]);
                //Verify if password has minimum condition to create.
                passwordStrength($request->input('password'));

                $user = $this->platformUser->create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'platform_id' => Auth::user()->platform_id,
                ]);
            }
            $this->linkUserToPlatform($user->id, Auth::user()->platform_id, $request->input('type_access'), $request->input('permission_id'));
            return redirect('/platform-config/users');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function UserEdit($id)
    {
        try {
            $user = PlatformUser::find($id);

            $user->type_access = $user->platform_access()->wherePlatformId(auth::user()->platform_id)->first()->type_access;

            //checa se usuário existe em outras plataformas
            $total_in_other_platforms = $this->checkTotalOthersPlatform($id, Auth::user()->platform_id);

            (new SecurityHelper)->securityUserByDB($id);

            $permissions = $user->platform->permissions()->get()->pluck('name', 'id');

            $platform_d = auth::user()->platform_id;

            $permission_id = $user->type_access == 'restrict' ?
                $user->platform_access()->wherePlatformId($platform_d)->first()->permission_id :
                null;

            return view('usersPlatform.edit', compact('user', 'permission_id', 'total_in_other_platforms', 'permissions'));
        } catch (Exception $e) {
            return redirect('/platform-config/users')->with('error', 'Erro. ' . $e->getMessage());
        }
    }

    public function UserUpdate(Request $request, $id)
    {
        try {
            (new SecurityHelper)->securityUserByDB($id);
            //checa se usuário existe em outras plataformas
            $total_in_other_platforms = $this->checkTotalOthersPlatform($id, Auth::user()->platform_id);
            $user = $this->platformUser->find($id);
            $this->linkUserToPlatform($user->id, Auth::user()->platform_id, $request->input('type_access'), $request->input('permission_id'));

            if ($total_in_other_platforms == 0)
                $this->UserSave($request, $user);

            return redirect('/platform-config/users');
        } catch (Exception $e) {
            return redirect('/platform-config/users')->with('error', 'Erro. ' . $e->getMessage());
        }
    }

    private function UserSave($request, $user)
    {
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();
    }

    private function linkUserToPlatform($user_id, $platform_id, $type_access, $permission_id)
    {
        $user = $this->platformUser->withTrashed()->find($user_id);
        if ($user != null) $user->restore();

        $platform_user = PlatformUserAccess::where('platform_id', $platform_id)
            ->where('platforms_users_id', $user_id)
            ->first();

        if ($type_access == 'full')
            $permission_id = null;

        $data = [
            'type_access' => $type_access,
            'permission_id' => $permission_id
        ];

        if (!$platform_user) {
            $data = array_merge(
                $data,
                [
                    'platform_id' => $platform_id,
                    'platforms_users_id' => $user_id
                ]
            );
            PlatformUserAccess::create($data);
        } else {
            PlatformUserAccess::where('platform_id', $platform_id)
                ->where('platforms_users_id', $user_id)
                ->update($data);
        }
    }

    private function checkTotalOthersPlatform($user_id, $platform_id)
    {
        $total = DB::table('platform_user')
            ->where('platform_id', '<>', Auth::user()->platform_id)
            ->where('platforms_users_id', $user_id)
            ->count();

        return $total;
    }

    private function checkUserExist($email)
    {
        $user = $this->platformUser
            ->select('id', 'name')
            ->where('email', $email)
            ->withTrashed()
            ->first();
        return $user;
    }


    public function UserDestroy($id)
    {
        try {
            (new SecurityHelper)->securityUserByDB($id);
            $this->platformUserAccess
                ->where('platform_id', Auth::user()->platform_id)
                ->where('platforms_users_id', $id)
                ->delete();
            return response()->json(['message' => 'Usuário deletado com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Não foi possível remover o usuário.', 400]);
        }
    }


    public function validUrlOfficial(Request $request)
    {
        $result = dns_get_record($request->url_official, DNS_CNAME);

        if (count($result) > 0) {
            foreach ($result as $row) {
                if (isset($row['type']) && $row['type'] === 'CNAME') {
                    $platform = $this->platform->find(Auth::user()->platform_id);
                    $platform->update(['url_official' => $request->url_official]);
                    return ['status' => 'success', 'message' => "CNAME respondendo com sucesso! Endereço salvo com sucesso!"];
                }
            }
        }
        $message = "Não identificamos a configuração no servidor DNS, em geral essa alteração pode levar até 72 horas para fazer efeito. Consulte seu provedor.";
        return ['status' => 'error', 'message' => $message];
    }

    public function onOff(Request $request)
    {
        $status = 1;
        $platform = $this->platform->find(Auth::user()->platform_id);

        if ($request->checked) {
            $message = "Plataforma ativada com sucesso!";
        } else {
            $status = 0;
            $message = "Plataforma desativada com sucesso!";
        }

        $platform->active = $status;
        $platform->save();

        return ['status' => 'success', 'message' => $message];
    }
}
