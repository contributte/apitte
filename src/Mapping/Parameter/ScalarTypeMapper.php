<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

class ScalarTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function normalize($value)
	{
		if ($value === null || $value === '') {
			return null;
		}

		// boolean
		if (is_bool($value)) {
			return $value;
		}

		if ($value === '1' || $value === 'true') {
			return true;
		}

		if ($value === '0' || $value === 'false') {
			return false;
		}

		// int
		if (is_int($value) || (is_string($value) && preg_match('#^-?[0-9]+\z#', $value))) {
			return (int) $value;
		}

		// float
		if (is_float($value) || (is_string($value) && preg_match('#^-?[0-9]*[.]?[0-9]+\z#', $value))) {
			return (float) $value;
		}

		return (string) $value;
	}

}
