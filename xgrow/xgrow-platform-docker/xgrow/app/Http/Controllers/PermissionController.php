<?php

namespace App\Http\Controllers;

use App\Permission;
use App\PlatformUser;
use App\Platform;
use App\RoleCategory;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use stdClass;

class PermissionController extends Controller
{
    private $permission;
    private $role;
    private $category;
    private $platform_user;
    private $user;
    private $platform;

    public function __construct(Permission $permission, Role $role, RoleCategory $category, PlatformUser $platform_user, Platform $platform)
    {
        $this->permission = $permission;
        $this->role = $role;
        $this->category = $category;
        $this->platform_user = $platform_user;
        $this->platform = $platform;
    }

    public function index()
    {
        $permissions = $this->permission->where('platform_id', Auth::user()->platform_id)->get();
        $total_label = $permissions->count();
        return view('permission.index', compact('total_label', 'permissions'));
    }

    public function create()
    {
        $permission = new stdClass;
        $permission->id = 0;
        $categories = $this->category->orderBy('order', 'ASC')->get();
        return view('permission.model', compact('permission', 'categories'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->save($request);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Collaborator  $collaborator
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        $categories = $this->category->orderBy('order', 'ASC')->get();
        return view('permission.model', compact('permission', 'categories'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Teste  $teste
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->save($request, $id);
    }

    public function save($request, $id = 0)
    {

        if (!$request->name) {
            return back()->withInput()->withErrors(['name' => 'Informe o nome do grupo']);
        } else if (!$request->roles) {
            return back()->withInput()->withErrors(['permissions' => 'Você deve selecionar as atribuições']);
        }
        // else if (!isset($request->users[1])) {
        //     return back()->withInput()->withErrors(['number_users' => 'Selecione os usuários do grupo']);
        // }


        $platform_id = Auth::user()->platform_id;

        $request->request->add(['platform_id' => $platform_id]);

        $permission  = Permission::updateOrCreate(['id' => $id], $request->all());

        $permission->roles()->sync($request->roles);

        //$this->platform_user->permissions()->where('permission_id', $permission->id)->update(['permission_id' => null]);

        DB::table('platform_user')->where('permission_id', $permission->id)->update(['permission_id' => null]);

        if (isset($request->users[1])) {
            foreach ($request->users[1] as $user) {
                DB::table('platform_user')->where('platform_id', $platform_id)->where('platforms_users_id', $user)
                    ->update(['permission_id' => $permission->id]);
            }
        }

        return redirect()->route('permission.index');
    }


    public function getUsers(Request $request)
    {
        try {

            $platform_id = Auth::user()->platform_id;

            $platform = $this->platform->find($platform_id);

            //sem permissão
            $users_permission[0] = [];

            //com permissão
            $users_permission[1] = [];

            $users = $platform->users()->where('type_access', 'restrict')->get();

            foreach ($users as $user) {
                $column = ($user->permissions()->where('permission_id', $request->permission_id)->count() == 0) ? 0 : 1;
                $users_permission[$column][] = $user;
            }

            return response()->json([
                'users_permission' => $users_permission,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Author $author
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Permission $permission)
    {
        $this->platform_user->permissions()->where('permission_id', $permission->id)
            ->update(['permission_id' => null]);
        $permission->delete();
        return redirect()->route('permission.index');
    }
}
