<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use DateTimeImmutable;
use TypeError;

class DateTimeTypeMapper implements ITypeMapper
{

	/**
	 * @inheritDoc
	 */
	public function normalize(mixed $value, array $options = []): ?DateTimeImmutable
	{
		try {
			$value = DateTimeImmutable::createFromFormat(DATE_ATOM, $value);
		} catch (TypeError $e) {
			throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_DATETIME);
		}

		if ($value !== false) {
			return $value;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_DATETIME);
	}

}
