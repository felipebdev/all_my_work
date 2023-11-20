<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

namespace App\Data;

use App\SDK\BigID\BigID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Exception;
use \RuntimeException;
/**
 * Class Validator
 *
 * @class Validator
 * @package App\Data
 */
class Validator
{
	/**
	 * @var string
	 */
	public const MESSAGES =
	[
		'required' => 'Esse campo é obrigatório',
		'passwordconfirm' => 'O valor deste campo deve coincidir com o valor do campo de senha',
		'strongpassword' => 'A senha deverá conter ao menos um número, uma letra maiúscula e minúscula e um caractere especial',
		'match' => 'O valor deste campo deve coincidir com o campo %s',
		'email' => 'Insira um email válido',
		'cep' => 'Insira um cep válido',
		'cpf' => 'Insira um cpf válido',
		'cnpj' => 'Insira um cnpj válido',
		'phone' => 'Insira um telefone válido',
		'length' => 'Este campo deve conter %s caracteres',
		'minchars' => 'Este campo deve conter no mínimo %s caracteres',
		'maxchars' => 'Este campo deve conter no máximo %s caracteres',
		'unique' => 'Este %s já existe',
		'atleast' => 'É preciso selecionar ao menos %s registro(s)',
		'bigid' => 'Este %s não está registrado na Receita Federal',
	];

	/**
	 * Validator constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	private static function parseRules(array $rules): array
	{
		return array_map(static fn($rule) => self::parseRule($rule), $rules);
	}

	/**
	 * @param string $rule
	 *
	 * @return array
	 */
	private static function parseRule(string $rule): array
	{
		$result = [];
		$rules = explode('|', $rule);

		foreach ($rules as $current)
		{
			$fragment = explode(':', $current);
			$result[] = ['rule' => trim($fragment[0]), 'params' => array_map(static fn(string $param) => trim($param ?? ''), explode(',', $fragment[1] ?? ''))];
		}

		return $result;
	}

	/**
	 * @param array $validations
	 *
	 * @return bool
	 */
	public static function checkSuccess(array $validations): bool
	{
		$result = [];
		foreach ($validations as $name => $rules) foreach ($rules as $rule) $result[] = $rule['result'];

		return in_array(false, $result, true) !== true;
	}

	/**
	 * @param array $values
	 * @param array $rules
	 *
	 * @return array
	 */
	public static function validate(array $values = [], array $rules = []): array
	{
		$rules = self::parseRules($rules);

		$result = [];

		foreach ($values as $key => $value)
		{
			$validations = [];
			if (isset($rules[$key])) foreach ($rules[$key] as $rule) $validations[] = ['rule' => $rule['rule'], 'params' => $rule['params'], 'result' => forward_static_call([__CLASS__, $rule['rule']], $value, $rule['params'])];

			$result[$key] = $validations;
		}

		return $result;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function required(string $value = '', $params = null): bool
	{
		return isset($value) && !empty($value) && $value !== '';
	}
	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function bigid(string $value = '', $params = null): bool
	{
		if (!$params || !self::required($value)) return false;

		$bigID = new BigID();

		$ruleName = trim($params[0]);

		if($ruleName === 'cpf') return $bigID->validateCPF($value);
		else if($ruleName === 'cnpj') return $bigID->validateCNPJ($value);

		throw new \Exception('Validation::bigid unknown params: [' . implode(', ', $params) . '], value: ' . $value);
	}
	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function passwordconfirm(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return $value === (is_array($params) ? $params[0] : $params);
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function strongpassword(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		$composition = self::passwordComposition($value);
		return $composition['lowerCaseLetter'] && $composition['upperCaseLetter'] && $composition['digit'] && $composition['specialCharacter'];
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function match(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return $value === $params;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function email(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function cep(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return strlen($value) === 8 || strlen($value) === 10;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function cpf(string $value = '', $params = null): bool
	{
		if (!self::required($value) || (strlen($value) !== 11 && strlen($value) !== 14)) return false;

		$cpf = preg_replace( '/[^0-9]/is', '', $value );

		if (preg_match('/(\d)\1{10}/', $cpf)) return false;

		for ($t = 9; $t < 11; ++$t)
		{
			for ($d = 0, $c = 0; $c < $t; ++$c) $d += $cpf[$c] * (($t + 1) - $c);

			$d = ((10 * $d) % 11) % 10;

			if ((int) $cpf[$c] !== $d) return false;
		}
		return true;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function cnpj(string $value = '', $params = null):bool
	{
		if (!self::required($value) || (strlen($value) !== 14 && strlen($value) !== 18)) return false;

		$cnpj = preg_replace('/[^0-9]/', '', (string) $value);

		if (preg_match('/(\d)\1{13}/', $cnpj)) return false;

		for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++)
		{
			$sum += $cnpj[$i] * $j;
			$j = ($j === 2) ? 9 : $j - 1;
		}

		$rest = $sum % 11;

		if ((int) $cnpj[12] !== ($rest < 2 ? 0 : 11 - $rest)) return false;

		for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++)
		{
			$sum += $cnpj[$i] * $j;
			$j = ($j === 2) ? 9 : $j - 1;
		}

		$rest = $sum % 11;

		return (int) $cnpj[13] === ($rest < 2 ? 0 : 11 - $rest);
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function phone(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return false;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function length(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return strlen($value) === (int)$params;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function minchars(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return strlen($value) > (int)$params - 1;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function maxchars(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return strlen($value) < (int)$params + 1;
	}

	/**
	 * @param string $value
	 * @param null $params
	 *
	 * @return bool
	 */
	public static function unique(string $value = '', $params = null): bool
	{
		if (!self::required($value)) return false;
		return count(DB::table($params[0])->select($params[1])->where($params[1], '=', $value)->get($params[1])) === 0;
	}
	public static function atleast($value = null, $params = null)
	{
		if(!is_array($value)) return false;
		return count($value) >= (int) $params;
	}
	/**
	 * @param string $value
	 * @param int $minimumLength
	 *
	 * @return array
	 */
	public static function passwordComposition(string $value, int $minimumLength = 0): array
	{
		if (!$value) return ['lowerCaseLetter' => false, 'upperCaseLetter' => false, 'digit' => false, 'specialCharacter' => false, 'length' => 0, 'minimumLength' => false];

		return
			[
				'lowerCaseLetter' => preg_match('/[a-z]/', $value),
				'upperCaseLetter' => preg_match('/[A-Z]/', $value),
				'digit' => preg_match('/\d/', $value),
				'specialCharacter' => preg_match('/[^A-Za-z0-9]/', $value),
				'minimumLength' => $minimumLength === 0 || strlen($value) > $minimumLength - 1
			];
	}

	/**
	 * @param string|null $validation
	 * @param bool $required
	 *
	 * @return array
	 * @throws \Exception
	 */
	public static function parseRulesByAttribute(?string $validation = null, bool $required = false):array
	{
		$validations = [];

		if ($validation)
		{
			$validationRules = explode('|', $validation);

			foreach ($validationRules as $ruleValidation)
			{
				$currentRule = explode(':', $ruleValidation);
				$rule = trim($currentRule[0] ?? '');
				$params = explode(',', trim($currentRule[1] ?? ''));

				if ($rule)
				{
					try
					{
						$validations[] = ['rule' => $rule, 'params' => $params, 'message' => self::MESSAGES[$rule]];
					}
					catch (Exception $exception)
					{
						throw new RuntimeException("invalid rule ($rule), params ($params)");
					}
				}
			}
		}

		if ($required) $validations[] = ['rule' => 'required', 'params' => null, 'message' => self::MESSAGES['required']];

		return $validations;
	}
}
