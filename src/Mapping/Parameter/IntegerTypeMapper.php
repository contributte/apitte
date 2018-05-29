<?php

namespace Apitte\Core\Mapping\Parameter;

class IntegerTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return int|NULL
	 */
	public function normalize($value)
	{
		if ($value === NULL) {
			return $value;
		}

		return intval($value);
	}

}
