<?php declare(strict_types = 1);

namespace Apitte\Core\Utils;

final class Regex
{

	/**
	 * @return mixed
	 */
	public static function match(string $subject, string $pattern, int $flags = 0)
	{
		$ret = preg_match($pattern, $subject, $m, $flags);

		return $ret === 1 ? $m : null;
	}

	/**
	 * @return mixed
	 */
	public static function matchAll(string $subject, string $pattern, int $flags = PREG_SET_ORDER)
	{
		$ret = preg_match_all($pattern, $subject, $m, $flags);

		return $ret !== false ? $m : null;
	}

	/**
	 * @param mixed $replacement
	 * @return mixed
	 */
	public static function replace(string $subject, string $pattern, $replacement, int $limit = -1)
	{
		return preg_replace($pattern, $replacement, $subject, $limit);
	}

	/**
	 * @return mixed
	 */
	public static function replaceCallback(string $subject, string $pattern, callable $callback, int $limit = -1)
	{
		return preg_replace_callback($pattern, $callback, $subject, $limit);
	}

}
