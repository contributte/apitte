<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

interface ITypeMapper
{

	/**
	 * @param mixed $value
	 * @return mixed
	 * @throws InvalidArgumentTypeException
	 */
	public function normalize($value);

}
