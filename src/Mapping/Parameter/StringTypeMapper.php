<?php

namespace Apitte\Core\Mapping\Parameter;

class StringTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return string|null
	 */
	public function normalize($value)
	{
		if ($value === null) {
			return $value;
		}

		return strval($value);
	}

}
