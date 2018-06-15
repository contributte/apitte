<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

class IntegerTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?int
	{
		if ($value === null) {
			return $value;
		}

		return (int) $value;
	}

}
