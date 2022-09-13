<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\ITypeMapper;

class MyEmailTypeMapper implements ITypeMapper
{

	public function normalize($value): string
	{
		if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return $value;
		}

		throw new InvalidArgumentTypeException('email', 'Pass valid email address.');
	}

}
