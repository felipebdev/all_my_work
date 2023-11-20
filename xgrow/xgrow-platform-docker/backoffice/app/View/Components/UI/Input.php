<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;
use App\Data\Validator;

/**
 *
 */
class Input extends HTMLComponent
{
	/**
	 * @var array
	 */
	public const ICONS =
	[
		'invoice' => '<i class="fas fa-file-invoice"></i>',
		'company' => '<i class="fas fa-building"></i>',
		'bank' => '<i class="fas fa-piggy-bank"></i>',
		'address' => '<i class="fa fa-home"></i>',
		'country' => '<i class="fa fa-globe"></i>',
		'number' => '<i class="fas fa-sort-numeric-up"></i>',
		'complement' => '<i class="fas fa-sort-numeric-down"></i>',
		'district' => '<i class="fa fa-building"></i>',
		'city' => '<i class="fa fa-map"></i>',
		'state' => '<i class="fa fa-map-signs"></i>',
		'comment' => '<i class="fas fa-comment-dots"></i>',
		'exclamation-triangle' => '<i class="fas fa-exclamation-triangle"></i>',
		'close' => '<i class="fas fa-times"></i>',
		'check' => '<i class="fas fa-check"></i>',
		'check-double' => '<i class="fas fa-check-double"></i>',
		'percentage' => '<i class="fas fa-percent"></i>',
		'code' => '<i class="fas fa-code"></i>',
		'info' => '<i class="fas fa-info"></i>',
		'hash' => '<i class="fas fa-hashtag"></i>',
		'blanket' => '<i class="fas fa-blanket"></i>',
		'university' => '<i class="fas fa-university"></i>',
		'money-check-1' => '<i class="fas fa-money-check-alt"></i>',
		'money-check-2' => '<i class="fas fa-money-check"></i>',
		'receipt' => '<i class="fas fa-receipt"></i>',
		'search-dollar' => '<i class="fas fa-search-dollar"></i>',
		'hand-dolar' => '<i class="fas fa-hand-holding-usd"></i>',
		'currency' => '<i class="far fa-money-bill-alt"></i>',
		'school' => '<i class="fas fa-school"></i>',
		'create' => '<i class="fas fa-plus-circle"></i>',
		'update' => '<i class="fas fa-edit"></i>'
	];
	/**
	 * @var string
	 */
	public string $type = '';
	/**
	 * @var string
	 */
	public string $value = '';
	/**
	 * @var string
	 */
	public string $label = '';
	/**
	 * @var string
	 */
	public ?string $prepend = null;
	/**
	 * @var string
	 */
	public ?string $append = null;
	/**
	 * @var bool
	 */
	public bool $required = false;
	/**
	 * @var string|int
	 */
	public $maxlength = 0;
	/**
	 * @var array
	 */
	public array $validation = [];
	/**
	 * @var array
	 */
	public array $serverValidation = [];
	/**
	 * @var string
	 */
	public string $tip = '';
	/**
	 * @var string
	 */
	public string $state = 'idle';
	/**
	 * @var string|int
	 */
	public $schema = -1;
	/**
	 * @var string[]
	 */
	protected $except = ['schema', 'options'];
	/**
	 * @var string
	 */
	public string $tipBehavior = 'hover';
	/**
	 * @var string|null
	 */
	public ?string $prependIcon = null;
	/**
	 * @var string
	 */
	public string $group = '';
	/**
	 * @var bool
	 */
	public ?string $optional = null;
	/**
	 * @var bool
	 */
	public bool $readonly = false;

	/**
	 * Input constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $type
	 * @param string $value
	 * @param string $label
	 * @param string|null $prepend
	 * @param string|null $append
	 * @param $maxlength
	 * @param bool $required
	 * @param string|null $validation
	 * @param string|null $serverValidation
	 * @param string $tip
	 * @param string $state
	 * @param $schema
	 * @param string|null $prependIcon
	 * @param string $group
	 * @param string|null $optional
	 * @param bool $readonly
	 */
	public function __construct(string $id = '', string $name = '', string $class = '', string $type = 'text', string $value = '', string $label = '', ?string $prepend = null, ?string $append = null, $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false)
	{
		parent::__construct('input', true, $id, $name, $class);

		if ($type === 'datetime') $class .= 'datepicker';

		$this->defaultAttributes['data-type'] = $type;

		if (!in_array($type, ['password', 'radio', 'checkbox', 'file']))
		{
			$type = 'text';
		}

		if ($validation) $this->defaultAttributes['data-validation'] = $validation;
		if ($serverValidation) $this->defaultAttributes['data-server-validation'] = $serverValidation;

		$this->type = $type;
		$this->value = $value;
		$this->label = $label;
		$this->prepend = self::getIcon($prependIcon, $prepend);
		$this->append = $append;
		$this->maxlength = $maxlength;
		$this->required = $required;
		$this->validation = $this->parseValidation($validation, $required);
		$this->serverValidation = $this->parseValidation($serverValidation, $required);
		$this->tip = $tip;
		$this->state = $state;
		$this->schema = $schema;
		$this->group = $group;
		$this->optional = $optional;
		$this->readonly = $readonly;

		$this->defaultAttributes['data-state'] = $this->state;
		$this->defaultAttributes['data-schema'] = $this->schema;
		$this->defaultAttributes['data-group'] = $this->group;

		if ($optional) $this->defaultAttributes['data-optional'] = 'data-optional';
	}

	/**
	 * @param string|null $icon
	 * @param string|null $default
	 *
	 * @return string
	 */
	public static function getIcon(?string $icon = null, ?string $default = null): string
	{
		return self::ICONS[$icon] ?? $default ?? '';
	}

	/**
	 * @param string|null $validation
	 * @param bool $required
	 *
	 * @return array
	 * @throws \Exception
	 */
	private function parseValidation(?string $validation = null, bool $required = false): array
	{
		return Validator::parseRulesByAttribute($validation, $required);
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\Contracts\View\View|\Closure|string
	 */
	public function render()
	{
		return view('components.ui.input');
	}
}
