<?php

namespace Apitte\Core\Mapping\Parameter;

class StringTypeMapper extends AbstractTypeMapper
{

	/**
	 * @param mixed $value
	 * @return int
	 */
	public function normalize($value)
	{
		return strval($value);
	}

}
