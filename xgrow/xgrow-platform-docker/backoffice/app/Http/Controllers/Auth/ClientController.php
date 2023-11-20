<?php

namespace App\Http\Controllers\Auth;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use stdClass;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $clients = $this->client->all();
        return view('demo-content.groups.client.index', compact('clients'));
    }

    public function create()
    {
        $client = new stdClass;
        $client->id = 0;
        $client->type_person = 'J';
        $states = config('constants.states');
        return view('demo-content.groups.client.create', compact('client','states'));
    }

    public function edit($id)
    {
        $client = $this->client->find($id);
        $states = config('constants.states');
        return view('demo-content.groups.client.edit', compact('client','states'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules['cnpj'] = 'required_if:type_person,==,J';
        $rules['cpf'] = 'required_if:type_person,==,F';
        $rules['email'] = 'unique:clients';
        $rules['password'] = 'required|confirmed|min:6';

        if($request->id > 0){
            $client = $this->client->find($request->id);
            $rules['email'] .= ',email,' . $request->id;
            if(!(isset($request->change_password)))
                unset($rules['password']);
        }

        $validator = Validator::make($request->all(), $rules);

        if(!validateEmail($request->email)){
            $validator->after(function ($validator) {
                $validator->errors()->add('Email', 'Email inválido');
            });
        }

        $validator->validate();

        if(($request->id > 0) and !(isset($request->change_password))){
            $request->request->remove('password');
        }

        $client = $this->client->updateOrCreate(
            ['id' => $request->id],
            $request->all()
        );

       return redirect()->route('client.index');

    }

    public function destroy($id){
        $client = $this->client->find($id);
        $client->delete();
        return redirect()->route('client.index');
    }


}
