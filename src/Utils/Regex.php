<?php

namespace Apitte\Core\Utils;

final class Regex
{

	/**
	 * @param string $subject
	 * @param string $pattern
	 * @param int $flags
	 * @return mixed
	 */
	public static function match($subject, $pattern, $flags = 0)
	{
		$ret = preg_match($pattern, $subject, $m, $flags);

		return $ret === 1 ? $m : NULL;
	}

	/**
	 * @param string $subject
	 * @param string $pattern
	 * @param int $flags
	 * @return mixed
	 */
	public static function matchAll($subject, $pattern, $flags = PREG_SET_ORDER)
	{
		$ret = preg_match_all($pattern, $subject, $m, $flags);

		return $ret !== FALSE ? $m : NULL;
	}

	/**
	 * @param string $subject
	 * @param string $pattern
	 * @param mixed $replacement
	 * @param int $limit
	 * @return mixed
	 */
	public static function replace($subject, $pattern, $replacement, $limit = -1)
	{
		return preg_replace($pattern, $replacement, $subject, $limit);
	}

	/**
	 * @param string $subject
	 * @param string $pattern
	 * @param callable $callback
	 * @param int $limit
	 * @return mixed
	 */
	public static function replaceCallback($subject, $pattern, callable $callback, $limit = -1)
	{
		return preg_replace_callback($pattern, $callback, $subject, $limit);
	}

}
