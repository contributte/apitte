<?php

namespace Apitte\Core\Mapping\Parameter;

class FloatTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return int
	 */
	public function normalize($value)
	{
		return floatval($value);
	}

}
