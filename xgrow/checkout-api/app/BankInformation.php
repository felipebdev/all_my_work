<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankInformation extends Model
{
    protected $table = 'bank_information';

    protected $fillable = [
        'platform_user_id',
        'email',
        'document_type',
        'document',
        'holder_name',
        'account_type',
        'bank',
        'branch',
        'account',
        'branch_check_digit',
        'account_check_digit',
        'gateway_bank_id',
        'recipient_gateway',
        'recipient_id',
        'recipient_status',
        'recipient_reason',
        'recipient_pagarme',
        'used',
        'created_at',
        'updated_at',
    ];

    public static array $banksAllowed = [
        '237',
        '001',
        '341',
        '260',
        '197',
        '104',
        '033',
        '077',
        '336',
        '208',
        '422',
    ];

    //use HasFactory;
}
