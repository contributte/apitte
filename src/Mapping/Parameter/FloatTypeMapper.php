<?php

namespace Apitte\Core\Mapping\Parameter;

class FloatTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return float|null
	 */
	public function normalize($value)
	{
		if ($value === null) {
			return $value;
		}

		return floatval($value);
	}

}
