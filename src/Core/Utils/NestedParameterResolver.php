<?php declare(strict_types = 1);

namespace Apitte\Core\Utils;

/**
 * Utility class for resolving nested parameter names like JSON:API style
 * parameters (e.g., page[number], filter[status], page:number).
 */
final class NestedParameterResolver
{

	/**
	 * Parse a parameter name into path segments.
	 * Supports bracket notation (page[number]) and colon notation (page:number).
	 *
	 * @return string[]
	 */
	public static function parsePath(string $name): array
	{
		// If the name contains brackets, parse bracket notation
		if (str_contains($name, '[')) {
			return self::parseBracketNotation($name);
		}

		// If the name contains colons, parse colon notation
		if (str_contains($name, ':')) {
			return self::parseColonNotation($name);
		}

		// Simple parameter name
		return [$name];
	}

	/**
	 * Parse bracket notation like "page[number]" or "filter[status][]"
	 *
	 * @return string[]
	 */
	private static function parseBracketNotation(string $name): array
	{
		$segments = [];

		// Match the first part (before first bracket) and all bracket contents
		/** @var array<int, array<int, string>>|null $matches */
		$matches = Regex::matchAll($name, '#([^\[\]]+)|\[([^\[\]]*)\]#');

		if ($matches === null) {
			return [$name];
		}

		foreach ($matches as $match) {
			// Either the non-bracket part or the bracket content
			$segment = ($match[1] ?? '') !== '' ? $match[1] : ($match[2] ?? '');
			if ($segment !== '') {
				$segments[] = $segment;
			}
		}

		return $segments !== [] ? $segments : [$name];
	}

	/**
	 * Parse colon notation like "page:number"
	 *
	 * @return string[]
	 */
	private static function parseColonNotation(string $name): array
	{
		return explode(':', $name);
	}

	/**
	 * Check if a parameter name is nested (uses bracket or colon notation).
	 */
	public static function isNested(string $name): bool
	{
		return str_contains($name, '[') || str_contains($name, ':');
	}

	/**
	 * Get a value from a nested array using a parameter name path.
	 *
	 * @param array<string, mixed> $data
	 */
	public static function getValue(array $data, string $name, mixed $default = null): mixed
	{
		$path = self::parsePath($name);

		return self::getValueByPath($data, $path, $default);
	}

	/**
	 * Get a value from a nested array using a path array.
	 *
	 * @param array<string, mixed> $data
	 * @param string[] $path
	 */
	private static function getValueByPath(array $data, array $path, mixed $default = null): mixed
	{
		$current = $data;

		foreach ($path as $segment) {
			if (!is_array($current) || !array_key_exists($segment, $current)) {
				return $default;
			}

			$current = $current[$segment];
		}

		return $current;
	}

	/**
	 * Check if a value exists at the given parameter name path.
	 *
	 * @param array<string, mixed> $data
	 */
	public static function hasValue(array $data, string $name): bool
	{
		$path = self::parsePath($name);

		return self::hasValueByPath($data, $path);
	}

	/**
	 * Check if a value exists at the given path.
	 *
	 * @param array<string, mixed> $data
	 * @param string[] $path
	 */
	private static function hasValueByPath(array $data, array $path): bool
	{
		$current = $data;

		foreach ($path as $segment) {
			if (!is_array($current) || !array_key_exists($segment, $current)) {
				return false;
			}

			$current = $current[$segment];
		}

		return true;
	}

	/**
	 * Set a value in a nested array using a parameter name path.
	 *
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public static function setValue(array $data, string $name, mixed $value): array
	{
		$path = self::parsePath($name);

		return self::setValueByPath($data, $path, $value);
	}

	/**
	 * Set a value in a nested array using a path array.
	 *
	 * @param array<string, mixed> $data
	 * @param string[] $path
	 * @return array<string, mixed>
	 */
	private static function setValueByPath(array $data, array $path, mixed $value): array
	{
		if ($path === []) {
			return $data;
		}

		$key = array_shift($path);

		if ($path === []) {
			$data[$key] = $value;
		} else {
			if (!isset($data[$key]) || !is_array($data[$key])) {
				$data[$key] = [];
			}

			$data[$key] = self::setValueByPath($data[$key], $path, $value);
		}

		return $data;
	}

}
