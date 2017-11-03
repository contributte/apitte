<?php

namespace Apitte\Core\Mapping\Parameter;

interface ITypeMapper
{

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function normalize($value);

}
