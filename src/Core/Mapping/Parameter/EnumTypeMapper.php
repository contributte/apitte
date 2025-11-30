<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Schema\EndpointParameter;

class EnumTypeMapper implements ITypeMapper
{

	/**
	 * @inheritDoc
	 */
	public function normalize(mixed $value, array $options = []): string|int
	{
		/** @var EndpointParameter|null $endpoint */
		$endpoint = $options['endpoint'] ?? null;
		$enumValues = $endpoint?->getEnum() ?? [];

		if ($enumValues === [] || !in_array($value, $enumValues, true)) {
			throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_ENUM);
		}

		return $value;
	}

}
