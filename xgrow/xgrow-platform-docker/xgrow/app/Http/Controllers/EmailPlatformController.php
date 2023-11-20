<?php

namespace App\Http\Controllers;

use App\Mail\SendMailTest;
use App\Repositories\SubscriberEmails\SubscriberEmailsRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use StdClass;
use Auth;
use App\Email;
use App\EmailConfig;
use App\EmailPlatform;
use App\PlatformUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmailPlatformController extends Controller
{
    private $email;
    private $emailPlatform;
    private $platformUser;
    private $emailConfig;
    private SubscriberEmailsRepository $subscriberEmailsRepository;

    public function __construct(Email $email, EmailPlatform $emailPlatform, PlatformUser $platformUser, EmailConfig $emailConfig,
                                SubscriberEmailsRepository $subscriberEmailsRepository){
        $this->subscriberEmailsRepository = $subscriberEmailsRepository;
        $this->email = $email;
        $this->emailPlatform = $emailPlatform;
        $this->platformUser = $platformUser;
        $this->emailConfig = $emailConfig;
    }

    public function index(Request $request)
    {
        $query = $this->emailPlatform
            ->join('emails', 'emails.id', '=', 'email_platforms.email_id')
            ->where('platform_id', Auth::user()->platform_id)
            ->select('email_platforms.*', 'emails.area', 'emails.subject', 'email_platforms.subject as subjectUser');
        $custom = $query->paginate(10);

        $query = $this->email->select();
        $default = $query->paginate(10);

        return view('emails-platforms.index', compact('custom', 'default'));
    }

    public function create()
    {
        $data = [];
        $data["type"] = "create";

        $customizedEmails = $this->emailPlatform->select('email_id')
            ->where('platform_id', Auth::user()->platform_id)
            ->groupBy('email_id')
            ->get()
            ->pluck('email_id')
            ->toArray();
        $data["email_type"] = $email_type = $this->email->all()->whereNotIn('id', $customizedEmails)->sortBy('subject');

        $data["from"] = $this->platformUser->where('platform_id', '=', Auth::user()->platform_id)->get();

        $email = new StdClass;
        $email->message = $email_type[0]->message ?? '';
        $email->subject = '';

        return view('emails-platforms.create', compact('data', 'email'));
    }

    public function store(Request $request)
    {
        try {
            $email = new EmailPlatform;

            if (!isset($request->from)) {
                throw new Exception("Selecione um remetente para o seu email", 400);
            }

            $hasEmail = EmailPlatform::where('email_id', $request->input('email_id'))
                ->where('platform_id', Auth::user()->platform_id)
                ->first();

            if ($hasEmail) {
                throw new Exception('Já existe um email para este tipo de email.');
            }

            if (!$request->input('subject')) {
                throw new Exception('Assunto obrigatório');
            }

            $email->email_id = $request->email_id;
            $email->platform_id = Auth::user()->platform_id;
            $email->from = $request->from;
            $email->message = $request->message;
            $email->subject = $request->subject;
            $email->save();

            return redirect()->route('emails.index')->with('success', 'Email criado com sucesso.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data = [];

        $email = $this->emailPlatform->findOrFail($id);
        $data["email_type"] = $this->email->all();
        $data["from"] = $this->platformUser->where('platform_id', '=', Auth::user()->platform_id)->get();

        $data["type"] = "edit";
        return view('emails-platforms.edit', compact('data', 'email'));
    }

    public function update(Request $request, $id)
    {
        try {
            $email = $this->emailPlatform->find($id);

            if ($email->email_id != $request->input('email_id')) {
                $hasEmail = EmailPlatform::where('email_id', $request->input('email_id'))
                    ->where('platform_id', Auth::user()->platform_id)
                    ->first();

                if ($hasEmail) {
                    throw new Exception('Já existe um email para este tipo de email.');
                }
            }

            if (!$request->input('subject')) {
                throw new Exception('Assunto obrigatório');
            }

            $email->email_id = $request->email_id;
            $email->platform_id = Auth::user()->platform_id;
            $email->from = $request->from;
            $email->message = $request->message;
            $email->subject = $request->subject;
            $email->save();

            return redirect()->route('emails.index')->with('success', 'Email atualizado com sucesso.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $email = $this->emailPlatform->find($id);
            $email->delete();
            return response()->json(['response' => 'success']);
        } catch (Exception $e) {
            $message = 'Não foi possível remover a mensagem.';
            return response()->json(['response' => 'fail', 'message' => $message]);
        }
    }

    public function customize($id)
    {
        $default = $this->email->findOrFail($id);

        $email = $this->emailPlatform->where('email_id', $id)
            ->where('platform_id', Auth::user()->platform_id)
            ->first();

        $data = [];

        if ($email) {
            $data["email_type"] = $this->email->all();
            $data["from"] = $this->platformUser->where('platform_id', '=', Auth::user()->platform_id)->get();
            $data["type"] = "edit";
            return view('emails-platforms.edit', compact('data', 'email'));
        }

        $data["type"] = "create";

        $customizedEmails = $this->emailPlatform->select('email_id')
            ->where('platform_id', Auth::user()->platform_id)
            ->groupBy('email_id')
            ->get()
            ->pluck('email_id')
            ->toArray();
        $data["email_type"] = $email_type = $this->email->all()->whereNotIn('id', $customizedEmails)->sortBy('subject');

        $data["from"] = $this->platformUser->where('platform_id', '=', Auth::user()->platform_id)->get();
        $data["email_selected"] = $id;

        return view('emails-platforms.create', compact('data', 'email'));
    }

    public function ajaxEmailCustom()
    {
        $query = $this->emailPlatform
            ->join('emails', 'emails.id', '=', 'email_platforms.email_id')
            ->where('platform_id', Auth::user()->platform_id)
            ->whereNotIn('emails.id', [7])
            ->select('email_platforms.*', 'emails.area', 'emails.subject', 'email_platforms.subject as subjectUser');

        $emails = $query->get();
        return response()->json(['data' => $emails]);
    }

    public function ajaxEmailDefault()
    {
        $query = $this->email->select()->whereNotIn('emails.id', [7]);
        $emails = $query->get();
        return response()->json(['data' => $emails]);
    }

    public function getMessageExample($id)
    {
        $email = $this->email->find($id);
        return ['message' => $email->message];
    }

    public function confEmail()
    {
        $email = $this->emailConfig->where('platform_id', Auth::user()->platform_id)->first();
        return view('emails-platforms.config', compact('email'));
    }

    public function confEmailStore(Request $request)
    {
        $emailConfig = EmailConfig::where('platform_id', Auth::user()->platform_id)->first();

        $conf = $emailConfig ?? new EmailConfig;

        $message = "E-mail validado";

        if ($conf->from_address !== $request->from_address || $conf->server_user !== $request->server_user) {
            $conf->valid_email = 0;
            $message = "Email não validado! [REENVIAR TESTE]";
        }

        $conf->from_name = $request->from_name;
        $conf->from_address = $request->from_address;
        $conf->server_name = $request->server_name;
        $conf->server_port = $request->server_port;
        $conf->server_user = $request->server_user;
        $conf->server_password = base64_encode($request->server_password);
        $conf->platform_id = Auth::user()->platform_id;

        $conf->save();

        return redirect()->route('emails.conf')->with(['message' => $message]);
    }

    public function emailTest()
    {
        $emailConfig = $this->emailConfig->where('platform_id', Auth::user()->platform_id)->first();

        $user = Auth::user() ?? auth('api')->user();

        if (!isset($user) || $user === null) {
            return ['status' => 'error', 'message' => 'Falha ao buscar dados do usuário logado. Faça login novamente na plataforma.'];
        }

        //        $message = "Se você recebeu este e-mail significa que as configurações feitas na Fandone deram certo!";

        $message = "Se você recebeu esse e-mail, clique abaixo no link para validar esse e-mail: \n \n";
        $message .= "Clique <a href='" . config('app.url') . "/emails/valid/" . Auth::user()->platform_id . "/";
        $message .= base64_encode($emailConfig->from_address) . "'>aqui</a> para validar o e-mail " . $emailConfig->from_address;

        $emailData = [
            'subject' => "FANDONE - TESTE CONFIGURAÇÃO EMAIL",
            'message' => $message
        ];

        $usersTo = [$user->email];

        Mail::to($usersTo)->send(new SendMailTest($emailData));

        return ['message' => 'Email enviado com sucesso! Verifique sua caixa de e-mail.'];
    }

    public function validEmail($platform_id, $email)
    {
        $emailDecode = base64_decode($email);

        $emailPlatform = $this->emailConfig->where('platform_id', $platform_id)->where('from_address', $emailDecode)->first();

        if ($emailPlatform !== null) {
            $emailPlatform->valid_email = 1;
            $emailPlatform->save();

            return redirect()->route('emails.valid-email')->with(['message' => 'E-mail validado com sucesso!']);
        }
        return redirect()->route('emails.valid-email')->withErrors(['error' => 'Houve um erro na validação do e-mail!']);
    }

    public function validEmailChecked()
    {
        return view('emails-platforms.email-valid');
    }
}
