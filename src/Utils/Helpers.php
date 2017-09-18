<?php

namespace Apitte\Core\Utils;

final class Helpers
{

	/**
	 * @param string $str
	 * @return string
	 */
	public static function slashless($str)
	{
		return Regex::replace($str, '#/{2,}#', '/');
	}

}
