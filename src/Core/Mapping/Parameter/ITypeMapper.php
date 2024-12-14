<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

interface ITypeMapper
{

	/**
	 * @throws InvalidArgumentTypeException
	 */
	public function normalize(mixed $value/*, ?array $enumValues = null*/): mixed;

}
