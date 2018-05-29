<?php

namespace Apitte\Core\Mapping\Parameter;

class FloatTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return float|NULL
	 */
	public function normalize($value)
	{
		if ($value === NULL) {
			return $value;
		}

		return floatval($value);
	}

}
