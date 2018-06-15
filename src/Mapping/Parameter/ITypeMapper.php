<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

interface ITypeMapper
{

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function normalize($value);

}
