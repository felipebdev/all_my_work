<?php

namespace App\Http\Controllers;

use App\Data\Intl;
use Illuminate\Http\Request;
use App\Data\Net\HTTPResponse;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
	public function __construct()
	{
		Intl::ptBR();
	}

	public function index()
	{
		return view('test.index', []);
	}

	public function create()
	{
		return view('test.index', []);
	}

	public function edit($id)
	{
		return view('test.index', []);
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(Request $request)
	{
		return view('test.index', []);
	}

	public function destroy($id)
	{
		return view('test.index', []);
	}
}
