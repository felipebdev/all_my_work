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

/**
 * Class Validator
 *
 * @class Validator
 * @package App\Data
 */
class Validator
{
	/**
	 * Validator constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * @param array $validation
	 *
	 * @return bool
	 */
	public static function isAllValid(array $validation):bool
	{
		$isAllValid = true;

		foreach ($validation as $inputValidation)
		{
			if(is_array($inputValidation))
			{
				if(in_array(false, $inputValidation, true))
				{
					$isAllValid = false;
					break;
				}
			}
			else
			{
				if($inputValidation === false)
				{
					$isAllValid = false;
					break;
				}
			}
		}
		return $isAllValid;
	}
}
