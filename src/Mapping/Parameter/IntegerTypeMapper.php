<?php

namespace Apitte\Core\Mapping\Parameter;

class IntegerTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return int|null
	 */
	public function normalize($value)
	{
		if ($value === null) {
			return $value;
		}

		return intval($value);
	}

}
