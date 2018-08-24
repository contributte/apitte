<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

class StringTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?string
	{
		if ($value === null || $value === '') {
			return null;
		}

		return (string) $value;
	}

}
