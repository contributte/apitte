<?php declare(strict_types = 1);

namespace Apitte\Core\Utils;

final class Regex
{

	/**
	 * @param 0|256|512|768 $flags
	 * @return array<string|int, string>|null
	 */
	public static function match(string $subject, string $pattern, int $flags = 0): ?array
	{
		$ret = preg_match($pattern, $subject, $m, $flags);

		return $ret === 1 ? $m : null;
	}

	/**
	 * @return array<int, array<string|int, string>>|null
	 */
	public static function matchAll(string $subject, string $pattern, int $flags = PREG_SET_ORDER): ?array
	{
		$ret = preg_match_all($pattern, $subject, $m, $flags);

		return $ret !== false ? $m : null;
	}

	/**
	 * @param string|string[] $replacement
	 */
	public static function replace(string $subject, string $pattern, string|array $replacement, int $limit = -1): ?string
	{
		return preg_replace($pattern, $replacement, $subject, $limit);
	}

	public static function replaceCallback(string $subject, string $pattern, callable $callback, int $limit = -1): ?string
	{
		return preg_replace_callback($pattern, $callback, $subject, $limit);
	}

}
