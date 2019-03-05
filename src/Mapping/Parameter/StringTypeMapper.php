<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class StringTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?string
	{
		return (string) $value;
	}

	/**
	 * @param mixed $value
	 * @throws InvalidArgumentTypeException
	 */
	public function denormalize($value): string
	{
		return (string) $value;
	}

}
