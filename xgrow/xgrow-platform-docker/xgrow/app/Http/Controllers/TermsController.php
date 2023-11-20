<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function glossary(){
        return view('terms.glossary');
    }

    public function privacyPolicy(){
        return view('terms.privacy_policy');
    }

     public function xgrowTerms(){
        return view('terms.xgrow_terms');
    }
}
