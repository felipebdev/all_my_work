<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;

class AdminController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->paginate(10);
        return view('admin.index', compact('users'));
    }

    public function create(Request $request)
    {
        $data = [];

        $data["type"] = "create";

        return view('admin.create', compact('data'));
    }

    public function store(Request $request)
    {
        try {
            if (!is_null($request->input('password')) && !is_null($request->input('password_confirm'))) {
                if ($request->input('password') != $request->input('password_confirm')) {
                    Throw new \Exception('As senhas não coincidem.');
                }
            }
            passwordStrength($request->input('password'));
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('admin.index');
        } catch (\Exception $e) {
            return back()->withErrors(['password' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = [];

        $user = $this->user->findOrFail($id);

        $data["user"] = $user;
        $data["type"] = "edit";

        return view('admin.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $user = $this->user->find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('admin.index');
    }

    public function destroy($id)
    {
        $user = $this->user->find($id);
        $user->delete();
        return redirect()->route('admin.index');
    }
}
