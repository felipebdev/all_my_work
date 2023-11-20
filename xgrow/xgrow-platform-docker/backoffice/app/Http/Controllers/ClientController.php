<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('demo-content.groups.client.index', compact('clients'));
    }

    public function create()
    {
        $client = new Client;
        $client->id = 0;
        $client->type_person = 'J';
        $states = config('constants.states');
        return view('demo-content.groups.client.create', compact('client', 'states'));
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $states = config('constants.states');
        return view('demo-content.groups.client.edit', compact('client', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ClientRequest $request
     * @return RedirectResponse
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();
            $data["cpf"] = cleanCpfOrCnpj($request->cpf);
            $data["cnpj"] = cleanCpfOrCnpj($request->cnpj);
            Client::create($data);

            return redirect()->route('client.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Update a client.
     * @param ClientRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(ClientRequest $request, $id): RedirectResponse
    {
        try {
            $data = $request->all();
            $client = Client::findOrFail($id);
            $data["cpf"] = cleanCpfOrCnpj($request->cpf);
            $data["cnpj"] = cleanCpfOrCnpj($request->cnpj);
            $client->update($data);

            return redirect()->route('client.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Delete client by ID
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return redirect()->route('client.index');
    }
}
