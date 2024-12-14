<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class EnumTypeMapper implements ITypeMapper
{

	public function normalize(mixed $value, ?array $enumValues = null): string
	{
		if (!in_array($value, $enumValues)) {
			throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_ENUM);
		}

		return $value;
	}

}
