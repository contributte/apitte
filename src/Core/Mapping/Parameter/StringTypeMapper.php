<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class StringTypeMapper implements ITypeMapper
{

	/**
	 * @inheritDoc
	 */
	public function normalize(mixed $value, array $options = []): ?string
	{
		if (!is_scalar($value)) {
			throw new InvalidArgumentTypeException('string');
		}

		return (string) $value;
	}

}
