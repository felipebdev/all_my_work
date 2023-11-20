<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    const TYPE_PERSON_PHYSICAL = 'F';
    const TYPE_PERSON_JURIDIC = 'J';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'verified',
        'type_person',
        'cpf',
        'cnpj',
        'fantasy_name',
        'company_name',
        'company_url',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'zipcode',
        'percent_split',
        'bank',
        'branch',
        'account',
        'image_id',
        'holder_name',
        'account_type',
        'branch_check_digit',
        'account_check_digit',
        'phone_number',
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
        if ( !empty($password) ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

}
