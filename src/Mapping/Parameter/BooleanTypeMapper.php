<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class BooleanTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?bool
	{
		if ($value === null || $value === '') {
			return null;
		}

		if (is_bool($value)) {
			return $value;
		}

		if ($value === 'true') {
			return true;
		}

		if ($value === 'false') {
			return false;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_BOOLEAN);
	}

}
