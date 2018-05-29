<?php

namespace Apitte\Core\Mapping\Parameter;

class StringTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return string|NULL
	 */
	public function normalize($value)
	{
		if ($value === NULL) {
			return $value;
		}

		return strval($value);
	}

}
