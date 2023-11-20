<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Platform;
use App\PlatformUser;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;
use stdClass;

class PlatformUserController extends Controller
{
    public function index()
    {
        $data = [];

        $users = PlatformUser::with('platforms')
            ->orderBy('platforms_users.name', 'asc')
            ->get();

        $data["users"] = $users;

        return view('platforms-users.index', $data);
    }

    public function create()
    {
        $data = [];

        $platforms = Platform::all();

        $data["platforms"] = $platforms;
        $data["type"] = "create";

        $user = new stdClass;
        $user->platforms = collect([]);
        $data["user"] = $user;

        $data["user"]->active = 1;
        $data["user"]->platform_id = 0;

        return view('platforms-users.create', $data);
    }

    public function store(Request $request)
    {
            $user = new PlatformUser;

            $rules = [
                'user_name' => "required|string|unique:platforms_users,name",
                'user_email' => "required|email|unique:platforms_users,email"
            ];

            $validator = Validator::make($request->all(), $rules);

            try{
                if ($request->input('user_password') != $request->input('password_confirm')) {
                    $validator->after(function ($validator){
                        $validator->errors()->add('Senha', 'As senhas não coincidem.');
                    });
                }
                passwordStrength($request->input('user_password'));
            }
            catch (\Exception $e){
                $validator->after(function ($validator) use($e){
                    $validator->errors()->add('Senha', $e->getMessage());
                });
            }

            $validator->validate();

            $user->name = $request["user_name"];
            $user->email = $request["user_email"];
            $user->password = Hash::make($request["user_password"]);
            $user->active = $request["user_active"] ? 1 : 0;

            $user->save();
            $user->platforms()->attach($request["user-platform_id"]);

            return redirect('/platforms/users');
    }

    public function edit($id)
    {
        $data = [];

        $platforms = Platform::all();

        $user = PlatformUser::with('platforms')
            ->find($id);

        $data["platforms"] = $platforms;
        $data["user"] = $user;
        $data["type"] = "edit";

        return view('platforms-users.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'user_name' => "required|string|unique:platforms_users,name,{$id}",
            'user_email' => "required|email|unique:platforms_users,email,{$id}"
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!empty($request->input('user_password'))) {
            try {
                if ($request->input('user_password') != $request->input('password_confirm')) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add('Senha', 'As senhas não coincidem.');
                    });
                }
                passwordStrength($request->input('user_password'));
            } catch (\Exception $e) {
                $validator->after(function ($validator) use ($e) {
                    $validator->errors()->add('Senha', $e->getMessage());
                });
            }
        }

        $validator->validate();

        $user = PlatformUser::find($id);
        $user->name = $request->user_name;
        $user->email = $request->user_email;
        $user->active = $request->user_active ? 1 : 0;

        $user->save();
        $user->platforms()->sync($request["user-platform_id"]);

        return redirect('/platforms/users');
    }

    public function destroy($id)
    {
        $platform = PlatformUser::find($id);

        $platform->delete();

        return redirect('/platforms/users');
    }
}
