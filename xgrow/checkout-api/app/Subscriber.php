<?php

namespace App;

use App\Notifications\subscriberResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Subscriber extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'subscribers';

    protected $fillable = [
        'name', 'email', 'password', 'login', 'last_acess', 'accept_terms'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_TRIAL = 'trial';
    const STATUS_CANCELED = 'canceled';
    const STATUS_LEAD = 'lead';
    const STATUS_PENDING_PAYMENT = 'pending_payment';

    const TYPE_NATURAL_PERSON = 'natural_person';
    const TYPE_LEGAL_PERSON = 'legal_person';

    // caution: column document_type is four-character only
    const DOCUMENT_TYPE_CPF = 'CPF';
    const DOCUMENT_TYPE_CNPJ = 'CNPJ';
    const DOCUMENT_TYPE_PASSPORT = 'PP';
    const DOCUMENT_TYPE_OTHER_NATURAL = 'ONAT'; // Non-Brazilian natural person identification
    const DOCUMENT_TYPE_OTHER_LEGAL = 'OLEG'; // Non-Brazilian legal entity identification (eg: US EIN)

    protected $appends = ['status_description'];

    public function subscriptions(){
            return $this->hasMany(Subscription::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
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
        return ['email' => $this->email,
                'platform_id' => $this->platform_id,
                'name' => $this->name];
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

    static function allSources()
    {
        return [
            self::SOURCE_IMPORT => 'Importação',
            self::SOURCE_PLATFORM => 'Plataforma',
            self::SOURCE_INTEGRATION => 'Integração',
            self::SOURCE_CHECKOUT => 'Checkout'
        ];
    }

    public function delete()
    {
        Log::info("Removido Assinante ".$this->id. " (".$this->email.")");
        Log::info(json_encode(debug_backtrace()));
        return parent::delete();
    }

    static function allCountrys()
    {
        return [
            'BRA' => 'Brasil',
            'USA' => 'Estados Unidos',
            'PRT' => 'Portugal',
            'AFG' => 'Afeganistão',
            'ZAF' => 'África do Sul',
            'ALB' => 'Albânia',
            'DEU' => 'Alemanha',
            'AND' => 'Andorra',
            'AGO' => 'Angola',
            'AIA' => 'Anguilla',
            'ATA' => 'Antártida',
            'ATG' => 'Antígua e Barbuda',
            'ANT' => 'Antilhas Holandesas',
            'SAU' => 'Arábia Saudita',
            'DZA' => 'Argélia',
            'ARG' => 'Argentina',
            'ARM' => 'Armênia',
            'AUS' => 'Austrália',
            'AUT' => 'Áustria',
            'AZE' => 'Azerbaijão',
            'BHS' => 'Bahamas',
            'BHR' => 'Bahrein',
            'BGD' => 'Bangladesh',
            'BRB' => 'Barbados',
            'BLR' => 'Belarus',
            'BEL' => 'Bélgica',
            'BLZ' => 'Belize',
            'BEN' => 'Benin',
            'BMU' => 'Bermudas',
            'BOL' => 'Bolívia',
            'BIH' => 'Bósnia-Herzegóvina',
            'BWA' => 'Botsuana',
            'BRN' => 'Brunei',
            'BGR' => 'Bulgária',
            'BFA' => 'Burkina Fasso',
            'BDI' => 'Burundi',
            'BTN' => 'Butão',
            'CPV' => 'Cabo Verde',
            'CMR' => 'Camarões',
            'KHM' => 'Camboja',
            'CAN' => 'Canadá',
            'KAZ' => 'Cazaquistão',
            'TCD' => 'Chade',
            'CHL' => 'Chile',
            'CHN' => 'China',
            'CYP' => 'Chipre',
            'SGP' => 'Cingapura',
            'COL' => 'Colômbia',
            'COG' => 'Congo',
            'PRK' => 'Coréia do Norte',
            'KOR' => 'Coréia do Sul',
            'CIV' => 'Costa do Marfim',
            'CRI' => 'Costa Rica',
            'HRV' => 'Croácia (Hrvatska)',
            'CUB' => 'Cuba',
            'DNK' => 'Dinamarca',
            'DJI' => 'Djibuti',
            'DMA' => 'Dominica',
            'EGY' => 'Egito',
            'SLV' => 'El Salvador',
            'ARE' => 'Emirados Árabes Unidos',
            'ECU' => 'Equador',
            'ERI' => 'Eritréia',
            'SVK' => 'Eslováquia',
            'SVN' => 'Eslovênia',
            'ESP' => 'Espanha',
            'EST' => 'Estônia',
            'ETH' => 'Etiópia',
            'FJI' => 'Fiji',
            'PHL' => 'Filipinas',
            'FIN' => 'Finlândia',
            'FRA' => 'França',
            'GAB' => 'Gabão',
            'GMB' => 'Gâmbia',
            'GHA' => 'Gana',
            'GEO' => 'Geórgia',
            'GIB' => 'Gibraltar',
            'GBR' => 'Grã-Bretanha (Reino Unido, UK)',
            'GRD' => 'Granada',
            'GRC' => 'Grécia',
            'GRL' => 'Groelândia',
            'GLP' => 'Guadalupe',
            'GUM' => 'Guam (Território dos Estados Unidos)',
            'GTM' => 'Guatemala',
            'GGY' => 'Guernsey',
            'GUY' => 'Guiana',
            'GUF' => 'Guiana Francesa',
            'GIN' => 'Guiné',
            'GNQ' => 'Guiné Equatorial',
            'GNB' => 'Guiné-Bissau',
            'HTI' => 'Haiti',
            'NLD' => 'Holanda',
            'HND' => 'Honduras',
            'HKG' => 'Hong Kong',
            'HUN' => 'Hungria',
            'YEM' => 'Iêmen',
            'BVT' => 'Ilha Bouvet (Território da Noruega)',
            'IMN' => 'Ilha do Homem',
            'CXR' => 'Ilha Natal',
            'PCN' => 'Ilha Pitcairn',
            'REU' => 'Ilha Reunião',
            'ALA' => 'Ilhas Aland',
            'CYM' => 'Ilhas Cayman',
            'CCK' => 'Ilhas Cocos',
            'COM' => 'Ilhas Comores',
            'FRO' => 'Ilhas Faroes',
            'FLK' => 'Ilhas Falkland (Malvinas)',
            'SGS' => 'Ilhas Geórgia do Sul e Sandwich do Sul',
            'HMD' => 'Ilhas Heard e McDonald (Território da Austrália)',
            'MNP' => 'Ilhas Marianas do Norte',
            'MHL' => 'Ilhas Marshall',
            'UMI' => 'Ilhas Menores dos Estados Unidos',
            'NFK' => 'Ilhas Norfolk',
            'SYC' => 'Ilhas Seychelles',
            'SLB' => 'Ilhas Solomão',
            'SJM' => 'Ilhas Svalbard e Jan Mayen',
            'TKL' => 'Ilhas Tokelau',
            'TCA' => 'Ilhas Turks e Caicos',
            'VIR' => 'Ilhas Virgens (Estados Unidos)',
            'VGB' => 'Ilhas Virgens (Inglaterra)',
            'WLF' => 'Ilhas Wallis e Futuna',
            'IND' => 'índia',
            'IDN' => 'Indonésia',
            'IRN' => 'Irã',
            'IRQ' => 'Iraque',
            'IRL' => 'Irlanda',
            'ISL' => 'Islândia',
            'ISR' => 'Israel',
            'ITA' => 'Itália',
            'JAM' => 'Jamaica',
            'JPN' => 'Japão',
            'JEY' => 'Jersey',
            'JOR' => 'Jordânia',
            'KEN' => 'Kênia',
            'KIR' => 'Kiribati',
            'KWT' => 'Kuait',
            'LAO' => 'Laos',
            'LVA' => 'Látvia',
            'LSO' => 'Lesoto',
            'LBN' => 'Líbano',
            'LBR' => 'Libéria',
            'LBY' => 'Líbia',
            'LIE' => 'Liechtenstein',
            'LTU' => 'Lituânia',
            'LUX' => 'Luxemburgo',
            'MAC' => 'Macau',
            'MKD' => 'Macedônia (República Yugoslava)',
            'MDG' => 'Madagascar',
            'MYS' => 'Malásia',
            'MWI' => 'Malaui',
            'MDV' => 'Maldivas',
            'MLI' => 'Mali',
            'MLT' => 'Malta',
            'MAR' => 'Marrocos',
            'MTQ' => 'Martinica',
            'MUS' => 'Maurício',
            'MRT' => 'Mauritânia',
            'MYT' => 'Mayotte',
            'MEX' => 'México',
            'FSM' => 'Micronésia',
            'MOZ' => 'Moçambique',
            'MDA' => 'Moldova',
            'MCO' => 'Mônaco',
            'MNG' => 'Mongólia',
            'MNE' => 'Montenegro',
            'MSR' => 'Montserrat',
            'MMR' => 'Myanma',
            'NAM' => 'Namíbia',
            'NRU' => 'Nauru',
            'NPL' => 'Nepal',
            'NIC' => 'Nicarágua',
            'NER' => 'Níger',
            'NGA' => 'Nigéria',
            'NIU' => 'Niue',
            'NOR' => 'Noruega',
            'NCL' => 'Nova Caledônia',
            'OMN' => 'Nova Zelândia',
            'PLW' => 'Palau',
            'PAN' => 'Panamá',
            'PNG' => 'Papua-Nova Guiné',
            'PAK' => 'Paquistão',
            'PRY' => 'Paraguai',
            'PER' => 'Peru',
            'PYF' => 'Polinésia Francesa',
            'POL' => 'Polônia',
            'PRI' => 'Porto Rico',
            'QAT' => 'Qatar',
            'KGZ' => 'Quirguistão',
            'CAF' => 'República Centro-Africana',
            'COD' => 'República Democrática do Congo',
            'DOM' => 'República Dominicana',
            'CZE' => 'República Tcheca',
            'ROM' => 'Romênia',
            'RWA' => 'Ruanda',
            'RUS' => 'Rússia',
            'ESH' => 'Saara Ocidental',
            'VCT' => 'Saint Vincente e Granadinas',
            'ASM' => 'Samoa Americana',
            'WSM' => 'Samoa Ocidental',
            'SMR' => 'San Marino',
            'SHN' => 'Santa Helena',
            'LCA' => 'Santa Lúcia',
            'BLM' => 'São Bartolomeu',
            'KNA' => 'São Cristóvão e Névis',
            'MAF' => 'São Martim',
            'STP' => 'São Tomé e Príncipe',
            'SEN' => 'Senegal',
            'SLE' => 'Serra Leoa',
            'SRB' => 'Sérvia',
            'SYR' => 'Síria',
            'SOM' => 'Somália',
            'LKA' => 'Sri Lanka',
            'SPM' => 'St. Pierre and Miquelon',
            'SWZ' => 'Suazilândia',
            'SDN' => 'Sudão',
            'SWE' => 'Suécia',
            'CHE' => 'Suíça',
            'SUR' => 'Suriname',
            'TJK' => 'Tadjiquistão',
            'THA' => 'Tailândia',
            'TWN' => 'Taiwan',
            'TZA' => 'Tanzânia',
            'IOT' => 'Território Britânico do Oceano índico',
            'ATF' => 'Territórios do Sul da França',
            'PSE' => 'Territórios Palestinos Ocupados',
            'TMP' => 'Timor Leste',
            'TGO' => 'Togo',
            'TON' => 'Tonga',
            'TTO' => 'Trinidad and Tobago',
            'TUN' => 'Tunísia',
            'TKM' => 'Turcomenistão',
            'TUR' => 'Turquia',
            'TUV' => 'Tuvalu',
            'UKR' => 'Ucrânia',
            'UGA' => 'Uganda',
            'URY' => 'Uruguai',
            'UZB' => 'Uzbequistão',
            'VUT' => 'Vanuatu',
            'VAT' => 'Vaticano',
            'VEN' => 'Venezuela',
            'VNM' => 'Vietnã',
            'ZMB' => 'Zâmbia',
            'ZWE' => 'Zimbábue',
        ];
    }

    public static function converCountryCode($code)
    {
        $countries = array(
            "AF" =>	"AFG",
            "AL" =>	"ALB",
            "DZ" =>	"DZA",
            "AS" =>	"ASM",
            "AD" =>	"AND",
            "AO" =>	"AGO",
            "AI" =>	"AIA",
            "AQ" =>	"ATA",
            "AG" =>	"ATG",
            "AR" =>	"ARG",
            "AM" =>	"ARM",
            "AW" =>	"ABW",
            "AU" =>	"AUS",
            "AT" =>	"AUT",
            "AZ" =>	"AZE",
            "BS" =>	"BHS",
            "BH" =>	"BHR",
            "BD" =>	"BGD",
            "BB" =>	"BRB",
            "BY" =>	"BLR",
            "BE" =>	"BEL",
            "BZ" =>	"BLZ",
            "BJ" =>	"BEN",
            "BM" =>	"BMU",
            "BT" =>	"BTN",
            "BO" =>	"BOL",
            "BQ" =>	"BES",
            "BA" =>	"BIH",
            "BW" =>	"BWA",
            "BV" =>	"BVT",
            "BR" =>	"BRA",
            "IO" =>	"IOT",
            "BN" =>	"BRN",
            "BG" =>	"BGR",
            "BF" =>	"BFA",
            "BI" =>	"BDI",
            "CV" =>	"CPV",
            "KH" =>	"KHM",
            "CM" =>	"CMR",
            "CA" =>	"CAN",
            "KY" =>	"CYM",
            "CF" =>	"CAF",
            "TD" =>	"TCD",
            "CL" =>	"CHL",
            "CN" =>	"CHN",
            "CX" =>	"CXR",
            "CC" =>	"CCK",
            "CO" =>	"COL",
            "KM" =>	"COM",
            "CD" =>	"COD",
            "CG" =>	"COG",
            "CK" =>	"COK",
            "CR" =>	"CRI",
            "HR" =>	"HRV",
            "CU" =>	"CUB",
            "CW" =>	"CUW",
            "CY" =>	"CYP",
            "CZ" =>	"CZE",
            "CI" =>	"CIV",
            "DK" =>	"DNK",
            "DJ" =>	"DJI",
            "DM" =>	"DMA",
            "DO" =>	"DOM",
            "EC" =>	"ECU",
            "EG" =>	"EGY",
            "SV" =>	"SLV",
            "GQ" =>	"GNQ",
            "ER" =>	"ERI",
            "EE" =>	"EST",
            "SZ" =>	"SWZ",
            "ET" =>	"ETH",
            "FK" =>	"FLK",
            "FO" =>	"FRO",
            "FJ" =>	"FJI",
            "FI" =>	"FIN",
            "FR" =>	"FRA",
            "GF" =>	"GUF",
            "PF" =>	"PYF",
            "TF" =>	"ATF",
            "GA" =>	"GAB",
            "GM" =>	"GMB",
            "GE" =>	"GEO",
            "DE" =>	"DEU",
            "GH" =>	"GHA",
            "GI" =>	"GIB",
            "GR" =>	"GRC",
            "GL" =>	"GRL",
            "GD" =>	"GRD",
            "GP" =>	"GLP",
            "GU" =>	"GUM",
            "GT" =>	"GTM",
            "GG" =>	"GGY",
            "GN" =>	"GIN",
            "GW" =>	"GNB",
            "GY" =>	"GUY",
            "HT" =>	"HTI",
            "HM" =>	"HMD",
            "VA" =>	"VAT",
            "HN" =>	"HND",
            "HK" =>	"HKG",
            "HU" =>	"HUN",
            "IS" =>	"ISL",
            "IN" =>	"IND",
            "ID" =>	"IDN",
            "IR" =>	"IRN",
            "IQ" =>	"IRQ",
            "IE" =>	"IRL",
            "IM" =>	"IMN",
            "IL" =>	"ISR",
            "IT" =>	"ITA",
            "JM" =>	"JAM",
            "JP" =>	"JPN",
            "JE" =>	"JEY",
            "JO" =>	"JOR",
            "KZ" =>	"KAZ",
            "KE" =>	"KEN",
            "KI" =>	"KIR",
            "KP" =>	"PRK",
            "KR" =>	"KOR",
            "KW" =>	"KWT",
            "KG" =>	"KGZ",
            "LA" =>	"LAO",
            "LV" =>	"LVA",
            "LB" =>	"LBN",
            "LS" =>	"LSO",
            "LR" =>	"LBR",
            "LY" =>	"LBY",
            "LI" =>	"LIE",
            "LT" =>	"LTU",
            "LU" =>	"LUX",
            "MO" =>	"MAC",
            "MG" =>	"MDG",
            "MW" =>	"MWI",
            "MY" =>	"MYS",
            "MV" =>	"MDV",
            "ML" =>	"MLI",
            "MT" =>	"MLT",
            "MH" =>	"MHL",
            "MQ" =>	"MTQ",
            "MR" =>	"MRT",
            "MU" =>	"MUS",
            "YT" =>	"MYT",
            "MX" =>	"MEX",
            "FM" =>	"FSM",
            "MD" =>	"MDA",
            "MC" =>	"MCO",
            "MN" =>	"MNG",
            "ME" =>	"MNE",
            "MS" =>	"MSR",
            "MA" =>	"MAR",
            "MZ" =>	"MOZ",
            "MM" =>	"MMR",
            "NA" =>	"NAM",
            "NR" =>	"NRU",
            "NP" =>	"NPL",
            "NL" =>	"NLD",
            "NC" =>	"NCL",
            "NZ" =>	"NZL",
            "NI" =>	"NIC",
            "NE" =>	"NER",
            "NG" =>	"NGA",
            "NU" =>	"NIU",
            "NF" =>	"NFK",
            "MP" =>	"MNP",
            "NO" =>	"NOR",
            "OM" =>	"OMN",
            "PK" =>	"PAK",
            "PW" =>	"PLW",
            "PS" =>	"PSE",
            "PA" =>	"PAN",
            "PG" =>	"PNG",
            "PY" =>	"PRY",
            "PE" =>	"PER",
            "PH" =>	"PHL",
            "PN" =>	"PCN",
            "PL" =>	"POL",
            "PT" =>	"PRT",
            "PR" =>	"PRI",
            "QA" =>	"QAT",
            "MK" =>	"MKD",
            "RO" =>	"ROU",
            "RU" =>	"RUS",
            "RW" =>	"RWA",
            "RE" =>	"REU",
            "BL" =>	"BLM",
            "SH" =>	"SHN",
            "KN" =>	"KNA",
            "LC" =>	"LCA",
            "MF" =>	"MAF",
            "PM" =>	"SPM",
            "VC" =>	"VCT",
            "WS" =>	"WSM",
            "SM" =>	"SMR",
            "ST" =>	"STP",
            "SA" =>	"SAU",
            "SN" =>	"SEN",
            "RS" =>	"SRB",
            "SC" =>	"SYC",
            "SL" =>	"SLE",
            "SG" =>	"SGP",
            "SX" =>	"SXM",
            "SK" =>	"SVK",
            "SI" =>	"SVN",
            "SB" =>	"SLB",
            "SO" =>	"SOM",
            "ZA" =>	"ZAF",
            "GS" =>	"SGS",
            "SS" =>	"SSD",
            "ES" =>	"ESP",
            "LK" =>	"LKA",
            "SD" =>	"SDN",
            "SR" =>	"SUR",
            "SJ" =>	"SJM",
            "SE" =>	"SWE",
            "CH" =>	"CHE",
            "SY" =>	"SYR",
            "TW" =>	"TWN",
            "TJ" =>	"TJK",
            "TZ" =>	"TZA",
            "TH" =>	"THA",
            "TL" =>	"TLS",
            "TG" =>	"TGO",
            "TK" =>	"TKL",
            "TO" =>	"TON",
            "TT" =>	"TTO",
            "TN" =>	"TUN",
            "TR" =>	"TUR",
            "TM" =>	"TKM",
            "TC" =>	"TCA",
            "TV" =>	"TUV",
            "UG" =>	"UGA",
            "UA" =>	"UKR",
            "AE" =>	"ARE",
            "GB" =>	"GBR",
            "UM" =>	"UMI",
            "US" =>	"USA",
            "UY" =>	"URY",
            "UZ" =>	"UZB",
            "VU" =>	"VUT",
            "VE" =>	"VEN",
            "VN" =>	"VNM",
            "VG" =>	"VGB",
            "VI" =>	"VIR",
            "WF" =>	"WLF",
            "EH" =>	"ESH",
            "YE" =>	"YEM",
            "ZM" =>	"ZMB",
            "ZW" =>	"ZWE",
            "AX" =>	"ALA"
        );

        $return = null;
        if( array_key_exists ( $code , $countries )  )
        {
            $return = $countries[$code];
        }
        return $return;
    }

    static function allStates()
    {
        return [
            "AC" => 'AC',
            "AL" => 'AL',
            "AP" => 'AP',
            "AM" => 'AM',
            "BA" => 'BA',
            "CE" => 'CE',
            "DF" => 'DF',
            "ES" => 'ES',
            "GO" => 'GO',
            "MA" => 'MA',
            "MT" => 'MT',
            "MS" => 'MS',
            "MG" => 'MG',
            "PA" => 'PA',
            "PB" => 'PB',
            "PR" => 'PR',
            "PE" => 'PE',
            "PI" => 'PI',
            "RJ" => 'RJ',
            "RN" => 'RN',
            "RS" => 'RS',
            "RO" => 'RO',
            "RR" => 'RR',
            "SC" => 'SC',
            "SP" => 'SP',
            "SE" => 'SE',
            "TO" => 'TO'
        ];
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

    public function creditCard()
    {
        return $this->hasOne(CreditCard::class, 'id', 'credit_card_id');
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

        if($filter['filter_by_login'] > 0){
          $filter_by_login = ($filter['filter_by_login'] == 1) ? ' IS NOT NULL': ' IS NULL';
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

    static function getPlans($subscriber_id){
        $subscriber = self::find($subscriber_id);

        $subscription = $subscriber->subscriptions()->whereNull('payment_pendent')->whereStatus('active')->where(function($q){
            $q->whereNull('canceled_at')
                ->orWhere('canceled_at', '>', Carbon::now());
        });

        $subscription_plans = $subscription->pluck('plan_id')->toArray();

        return $subscription_plans;
    }

    public function getStatusDescriptionAttribute() {
        return self::allStatus()[$this->status] ?? null;
    }

    static function checkIfSubscriberHasUnlimitedPlans($subscriber_id){
        $plans = self::getPlans($subscriber_id);
        foreach ($plans as $id) {
           if(CoursePlan::where('plan_id', $id)->count() == 0 and SectionPlan::where('plan_id', $id)->count() == 0)
                return true;
        }
        return false;
    }

    public static function getSubscriber(string $email)
    {

        return self::where('email', $email)->first();
    }

}
