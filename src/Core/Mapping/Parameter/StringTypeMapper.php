<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

class StringTypeMapper implements ITypeMapper
{

	public function normalize(mixed $value): ?string
	{
		return (string) $value;
	}

}
