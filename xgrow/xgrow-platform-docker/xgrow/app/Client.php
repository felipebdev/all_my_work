<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Client extends Model
{
    const TYPE_PERSON_PHYSICAL = 'F';
    const TYPE_PERSON_JURIDIC = 'J';

    const PERCENT_SPLIT = 94.01;
    const TAX_TRANSACTION = 2.00;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'type_person', 'cpf', 'cnpj', 'fantasy_name', 'company_name', 'company_url', 'address', 'number', 'complement', 'district', 'city', 'state', 'zipcode', 'percent_split', 'bank', 'branch', 'account', 'image_id', 'holder_name', 'account_type', 'branch_check_digit', 'account_check_digit', 'phone_number', 'phone2', 'upload_directory', 'check_document_number', 'check_document_type', 'check_document_status, document_front_image_url, document_back_image_url', 'verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower($email);
    }

    public static function isRegistrationComplete(): bool
    {
        $client = Client::where('email', Auth::user()->email)->first();

        $result =
            [
                $client->type_person ?? null,
                $client->cnpj ?? $client->cpf  ?? null,
                $client->fantasy_name ?? null,
                $client->phone_number ?? null,
                $client->document_front_image_url ?? null,
                $client->document_back_image_url ?? null,
                $client->bank ?? null,
                $client->account_type ?? null,
                $client->branch ?? null,
                $client->account ?? null,
                $client->zipcode ?? null,
                $client->address ?? null,
                $client->number ?? null,
                $client->district ?? null,
                $client->city ?? null,
                $client->state ?? null,
            ];

        return !in_array(null, $result, true);
    }

    public static function isUserAClient(): bool
    {
        return (Client::where('email', Auth::user()->email)->first()->email ?? null) !== null && (User::where('email', Auth::user()->email)->first()->email ?? null) !== null;
    }
}
