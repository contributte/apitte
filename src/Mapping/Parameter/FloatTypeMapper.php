<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

class FloatTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?float
	{
		if ($value === null) {
			return $value;
		}

		return (float) $value;
	}

}
