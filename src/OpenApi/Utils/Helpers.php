<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Utils;

class Helpers
{

	/**
	 * @param mixed $left
	 * @param mixed $right
	 * @return mixed
	 */
	public static function merge($left, $right)
	{
		if (is_array($left) && is_array($right)) {
			reset($left);
			$firstKey = key($left);
			foreach ($left as $key => $val) {
				if ($firstKey === 0 && is_int($key)) {
					$right[] = $val;
				} else {
					if (isset($right[$key])) {
						$val = static::merge($val, $right[$key]);
					}

					$right[$key] = $val;
				}
			}

			return $right;
		}

		if ($left === null && is_array($right)) {
			return $right;
		}

		return $left;
	}

}
