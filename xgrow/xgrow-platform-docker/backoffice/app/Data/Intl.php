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
 * Class Intl
 *
 * @class Intl
 * @package App\Data
 */
class Intl
{
	/**
	 * Intl constructor.
	 */
	private function __construct()
	{

	}
	/**
	 *
	 */
	public static function ptBR():void
	{
		setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');
	}
}
