<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

namespace App\Data\Object;

use \Exception;
use \stdClass;
/**
 * Class DynamicObject
 *
 * @class DynamicObject
 * @package App\Data\Object
 */
/**
 * Class Object
 * @package TheOne
 */
abstract class DynamicObject
{
	/**
	 * @param $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if(method_exists($this, 'get' . str_replace('_', '', ucwords($name, '_')))) return $this->{'get' . str_replace('_', '', ucwords($name, '_'))}();
		if(method_exists($this, 'get' . ucfirst($name))) return $this->{'get' . ucfirst($name)}();
		if(method_exists($this, $name)) return $this->{$name}();
	}
	/**
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		if(method_exists($this, 'set' . str_replace('_', '', ucwords($name, '_')))) return $this->{'set' . str_replace('_', '', ucwords($name, '_'))}($value);
		if(method_exists($this, 'set' . ucfirst($name))) return $this->{'set' . ucfirst($name)}($value);
		if(method_exists($this, $name)) return $this->{$name}($value);
	}
	public function __isset($name)
	{
		return (method_exists($this, 'set' . str_replace('_', '', ucwords($name, '_')))) || (method_exists($this, 'set' . ucfirst($name))) || (method_exists($this, $name));
	}
	/**
	 * @return stdClass
	 */
	public function toObject():stdClass
	{
		return json_decode(json_encode((object) $this->toArray()), false);
	}
	/**
	 * @return array
	 */
	public function toArray():array
	{
		$gettersSetters = array_filter(get_class_methods(get_class ($this)), function($method) { return substr($method, 0, 3) == 'get'; });

		return array_merge
		(
			get_object_vars($this),
			array_combine
			(
				$gettersSetters,
				array_map
				(
					function($method)
					{
						try
						{
							return $this->{$method}();
						}
						catch (\ArgumentCountError $exception)
						{
							return "$method cannot be called";
						}
					},
					$gettersSetters
				)
			)
		);
	}
	/**
	 * @return string
	 */
	public function toJSON():string
	{
		return json_encode($this->toArray());
	}
	/**
	 * @return string
	 */
	public function __toString():string
	{
		return (string) print_r($this->toArray(), true);
	}
	/**
	 * Destroy object reference
	 */
	public function __destruct()
	{
		//foreach ($this as $k => $v) try { $this->{$k} = null; } catch (\Exception $exception) {}
	}
}
