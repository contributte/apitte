<?php declare(strict_types = 1);

namespace Apitte\Core\Utils;

use Throwable;

final class Helpers
{

	public static function slashless(string $str): string
	{
		return Regex::replace($str, '#/{2,}#', '/');
	}

	/**
	 * @return mixed[]
	 */
	public static function throwableToArray(Throwable $throwable): array
	{
		return [
			'code' => $throwable->getCode(),
			'message' => $throwable->getMessage(),
			'file' => $throwable->getFile(),
			'line' => $throwable->getLine(),
			'trace' => $throwable->getTraceAsString(),
		];
	}

}
