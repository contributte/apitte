<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

interface ITypeMapper
{

	/**
	 * @param array<string, mixed> $options
	 * @throws InvalidArgumentTypeException
	 */
	public function normalize(mixed $value, array $options = []): mixed;

}
