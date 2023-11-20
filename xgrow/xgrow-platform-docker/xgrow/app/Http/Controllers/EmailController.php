<?php

namespace App\Http\Controllers;

use App\Email;
use Illuminate\Http\Request;

class EmailController extends Controller
{

    private $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function index()
    {
        $areas = config('constants.email_areas');

        $emails = $this->email->paginate(10);

        return view('emails.index', compact('areas', 'emails'));
    }

    public function create()
    {
        $data = [];

        $data["type"] = "create";

        $data["areas"] = config('constants.email_areas');

        return view('emails.create',compact('data'));
    }


    public function store(Request $request)
    {
        $email = new Email;

        $email->area = $request->email_area;
        $email->subject = $request->subject;
        $email->from = $request->from;
        $email->message = $request->message;
        $email->save();

        return redirect()->route('emails.index');
    }

    public function edit($id)
    {
        $data = [];

        $email = $this->email->findOrFail($id);
        $email->area_email_name = config('constants.email_areas')[$email->area];

        $data["areas"] = config('constants.email_areas');
        $data["email"] = $email;
        $data["type"] = "edit";

        return view('emails.edit',compact('data'));
    }

    public function update(Request $request,$id)
    {
        $email =  $this->email->find($id);
        $email->area = $request->email_area;
        $email->subject = $request->subject;
        $email->from = $request->from;
        $email->message = $request->message;
        $email->save();

        return redirect()->route('emails.index');
    }

    public function destroy($id)
    {
        $email = $this->email->find($id);
        $email->delete();
        return redirect()->route('emails.index');
    }


}
