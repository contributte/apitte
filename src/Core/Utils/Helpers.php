<?php declare(strict_types = 1);

namespace Apitte\Core\Utils;

use Apitte\Core\Exception\Logical\InvalidArgumentException;

final class Helpers
{

	public static function slashless(string $str): string
	{
		return Regex::replace($str, '#/{2,}#', '/');
	}

	/**
	 * @param array{object,string} $callback
	 */
	public static function callback(array $callback): callable
	{
		if (!is_callable($callback)) {
			throw new InvalidArgumentException('Invalid callback given');
		}

		return $callback;
	}

}
