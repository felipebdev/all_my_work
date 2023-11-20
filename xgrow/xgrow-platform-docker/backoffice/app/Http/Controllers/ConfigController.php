<?php

namespace App\Http\Controllers;

use App\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function edit(Config $config)
    {
        $config = Config::first();
        return view('config.edit', array( 'config' => $config ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Config $config)
    {
        $config = Config::first();
        if( !$config ) {
            $config = new Config();
        }
        $config->bank = $request->bank;
        $config->branch = $request->branch;
        $config->account = $request->account;
        $config->name = $request->name;
        $config->email = $request->email;
        $config->document = $request->document;
        $config->save();

        return redirect(route('configs.edit'));
    }
}

