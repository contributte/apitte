<?php

namespace Apitte\Core\Mapping\Parameter;

class IntegerTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return int
	 */
	public function normalize($value)
	{
		return intval($value);
	}

}
