<?php

namespace App;

use App\Http\Traits\ElasticsearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
	use SoftDeletes, HasFactory, ElasticsearchTrait;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'type_person', 'cpf', 'cnpj', 'fantasy_name', 'company_name',
        'company_url', 'created_at', 'address', 'number', 'complement', 'district', 'city', 'state', 'zipcode', 'percent_split',
        'tax_transaction', 'bank', 'branch', 'account', 'recipient_id', 'customer_id', 'statement_descriptor',
        'image_id', 'holder_name', 'account_type', 'branch_check_digit', 'account_check_digit', 'phone_number',
        'is_default_antecipation_tax', 'phone_country_code', 'phone_area_code', 'phone_number_code', 'phone2',
        'upload_directory', 'check_document_number', 'check_document_type', 'check_document_status',
        'document_front_image_url', 'document_back_image_url', 'image_id', 'cover'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function platforms(){
        return $this->hasMany(Platform::class, 'customer_id', 'id');
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function image(){
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function setPasswordAttribute($password)
    {
        if ( !empty($password) ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

}
