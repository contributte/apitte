<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class StringTypeMapper implements ITypeMapper
{

	public function normalize(mixed $value): ?string
	{
		if (!is_scalar($value)) {
			throw new InvalidArgumentTypeException('string');
		}

		return (string) $value;
	}

}
