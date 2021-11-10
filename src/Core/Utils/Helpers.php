<?php declare(strict_types = 1);

namespace Apitte\Core\Utils;

final class Helpers
{

	public static function slashless(string $str): string
	{
		return Regex::replace($str, '#/{2,}#', '/');
	}

}
