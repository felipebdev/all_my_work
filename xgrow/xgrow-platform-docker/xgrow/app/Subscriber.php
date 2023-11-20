<?php

namespace App;

use App\Helpers\GeographicCodes\BrazilianFederativeUnits;
use App\Helpers\GeographicCodes\CountryCodeIso3166;
use App\Notifications\subscriberResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\Integration\Models\Action;

class Subscriber extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'subscribers';

    protected $fillable = [
        'platform_id',
        'plan_id',
        'email',
        'name',
        'cel_phone',
        'password',
        'raw_password', // mutator
        'status',
        'address_country',
        'login',
        'last_acess',
        'accept_terms',
        'email_bounce_description',
        'document_type',
        'document_number',
        'source_register',
        'expo_la_token'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_TRIAL = 'trial';
    const STATUS_CANCELED = 'canceled';
    const STATUS_LEAD = 'lead';
    const STATUS_PENDING_PAYMENT = 'pending_payment';

    const TYPE_NATURAL_PERSON = 'natural_person';
    const TYPE_LEGAL_PERSON = 'legal_person';

    const DOCUMENT_TYPE_CPF = 'CPF';
    const DOCUMENT_TYPE_CNPJ = 'CNPJ';
    const DOCUMENT_TYPE_OTHER = 'OTHER';

    protected $appends = ['status_description'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'platform_id' => $this->platform_id,
            'name' => $this->name
        ];
    }

    /**
     * @param $value
     * @return string
     */
    public function getEmailBounceDescriptionAttribute($value)
    {
        if ($value === 'The server was unable to deliver your message (ex: unknown user, mailbox not found).') {

            return "E-mail inválido. Verifique o email com o cliente. '$value'";
        }

        return $value;
    }

    static function allGenders()
    {
        return [
            'male' => 'Masculino',
            'female' => 'Feminino',
            'not_specified' => 'Prefiro não dizer',
        ];
    }

    const SOURCE_IMPORT = 'import';
    const SOURCE_PLATFORM = 'platform';
    const SOURCE_INTEGRATION = 'integration';
    const SOURCE_CHECKOUT = 'checkout';
    const SOURCE_TMB = 'tmb';

    static function allSources()
    {
        return [
            self::SOURCE_IMPORT => 'Importação',
            self::SOURCE_PLATFORM => 'Plataforma',
            self::SOURCE_INTEGRATION => 'Integração',
            self::SOURCE_CHECKOUT => 'Checkout',
            self::SOURCE_TMB => 'TMB',
        ];
    }

    public function delete()
    {
        Log::info("Removido Assinante " . $this->id . " (" . $this->email . ")");
        Log::info(json_encode(debug_backtrace()));
        return parent::delete();
    }

    static function allCountrys()
    {
        return CountryCodeIso3166::ALPHA3_TO_NAME;
    }

    public static function converCountryCode($code)
    {
        return CountryCodeIso3166::fromAlpha2ToAlpha3($code);
    }

    static function allStates()
    {
        return BrazilianFederativeUnits::ACRONYM;
    }

    static function allStatus()
    {
        return [
            'active' => 'Ativo',
            'trial' => 'Trial',
            'inactive' => 'Inativo',
            'pending_payment' => 'Pagamento Pendente',
            'canceled' => 'Cancelado',
        ];
    }

    /**
     * Send the password reset notification.
     * @note: This override Authenticatable methodology
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new subscriberResetPasswordNotification($token));
    }

    public function integratable()
    {
        return $this->morphMany(IntegrationType::class, 'integratable');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function integration()
    {
        return $this->morphMany(IntegrationType::class, 'integratable');
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class)->withTimestamps();;
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function thumb()
    {
        //return $this->hasOne(File::class, 'id', 'photo');
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function attendants()
    {
        return $this->belongsToMany(Attendant::class, 'attendant_subscriber', 'attendant_id', 'subscriber_id');
    }

    // Mutators

    /**
     * Allows $subscriber->raw_password = '1234';
     *
     * @param $rawPassword
     */
    public function setRawPasswordAttribute($rawPassword)
    {
        $this->attributes['password'] = Hash::make($rawPassword);
    }

    public function markedForLogout()
    {
        $logout = AccessLog::select('type')
            ->where('user_id', $this->id)
            ->where('user_type', 'subscribers')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($logout !== null) {
            if ($logout->type === 'LOGIN') {
                $query = 'SELECT IF(DATE_ADD(MAX(created_at), INTERVAL 1 HOUR) < NOW(), 1, 0) AS logout
                  FROM content_logs a
                  WHERE user_type = "subscribers"
                  AND user_id = ' . $this->id;


                $result = DB::select($query);

                if (count($result) > 0) {

                    if ($result[0]->logout === 1) {
                        AccessLog::create([
                            'user_id' => $this->id,
                            'user_type' => $this->getTable(),
                            'type' => 'LOGOUT',
                            'description' => 'Usuário ' . $this->email . ' saiu do site [via api]',
                            'platform_id' => $this->platform_id,
                            'ip' => $_SERVER["REMOTE_ADDR"],
                            'browser_type' => AccessLog::searchBrowser('API'),
                            'device_type' => AccessLog::searchDevice('API')
                        ]);
                    }
                    return $result[0]->logout;
                } else {
                    return 0;
                }
            }
        }

        return 0;
    }

    public function markForLogout()
    {
        $this->logout = 1;
    }

    public function unmarkForLogout()
    {
        $this->logout = 0;
    }

    public function orderable()
    {
        return $this->morphMany('App\Order', 'orderable');
    }

    public function export(array $fields, array $filter, string $platform_id)
    {
        $fields = count($fields) == 0 ? 'a.*, b.name AS plan' : implode(', ', $fields);
        $sql = "SELECT a.name, a.email, a.document_type, " . $fields . ", b.name AS plan FROM subscribers a
        INNER JOIN plans b ON a.plan_id = b.id
        WHERE a.platform_id = '" . $platform_id . "'";

        if ($filter['filter_by_login'] > 0) {
            $filter_by_login = ($filter['filter_by_login'] == 1) ? ' IS NOT NULL' : ' IS NULL';
            $sql .=  " AND a.status = 'active' AND login" . $filter_by_login;
        }
        return $sql;
    }

    public function getSubscribers($platform_id, $lead)
    {
        return "select a.id, a.name, a.email, a.created_at, a.status, a.last_acess, b.name as plan_name, b.id as plan_id from subscribers a
                left join plans b on a.plan_id = b.id
                where a.platform_id = '$platform_id'
                and a.status != '$lead'
                ORDER BY a.id ASC";
    }

    static function getPlans($subscriber_id)
    {
        $subscriber = self::find($subscriber_id);

        $subscription = $subscriber->subscriptions()->whereNull('payment_pendent')->whereStatus('active')->where(function ($q) {
            $q->whereNull('canceled_at')
                ->orWhere('canceled_at', '>', Carbon::now());
        });

        $subscription_plans = $subscription->pluck('plan_id')->toArray();

        return $subscription_plans;
    }

    public function getStatusDescriptionAttribute()
    {
        return self::allStatus()[$this->status] ?? null;
    }

    static function checkIfSubscriberHasUnlimitedPlans($subscriber_id)
    {
        $plans = self::getPlans($subscriber_id);
        foreach ($plans as $id) {
            if (CoursePlan::where('plan_id', $id)->count() == 0 and SectionPlan::where('plan_id', $id)->count() == 0)
                return true;
        }
        return false;
    }

    /**
     *
     */
    public static function neverAccessed()
    {
        $actions = Action::whereNotNull('metadata')->where('event', 'onNeverAccessed')->get();

        $parameters = [];

        foreach ($actions as $action) {

            $parameters[] = [
                'action_id' => $action->id,
                'next_event' => Carbon::now()->format('Y-m-d'),
                'days_never_accessed_action' => $action->metadata['days_never_accessed'],
                'subscribers' => self::select('id as subscriber_id', 'login')
                    ->whereRaw("DATEDIFF(now(), login) >= {$action->metadata['days_never_accessed']}")
                    ->where('platform_id', $action->platform_id)
                    ->orWhereNull('login')
                    ->get()
            ];
        }

        $appActionsNeverAccessed = new AppActionsNeverAccessed();

        $appActionsNeverAccessed->storeActions($parameters);
    }

    /**
     * @param $bounce
     */
    public static function addBounceCase($bounce)
    {
        $subscriber = self::where('email', $bounce['Email'])
            ->where('email_bounce_id', $bounce['ID'])
            ->where('platform_id', $bounce['Tag'])
            ->first();

        if (!$subscriber) {

            Self::where('email', $bounce['Email'])
                ->where('platform_id', $bounce['Tag'])
                ->update([
                    'email_bounce_at' => $bounce['BouncedAt'],
                    'email_bounce_id' => $bounce['ID'],
                    'email_bounce_type' => $bounce['Type'],
                    'email_bounce_description' => $bounce['Description']
                ]);
        }
    }


}
