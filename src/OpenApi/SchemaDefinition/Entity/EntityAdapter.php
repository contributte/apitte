<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition\Entity;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\OpenApi\SchemaDefinition\BaseDefinition;
use DateTimeInterface;
use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use Nette\Utils\Type;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunctionAbstract;
use ReflectionProperty;
use Reflector;
use RuntimeException;

class EntityAdapter implements IEntityAdapter
{

	/**
	 * @return mixed[]
	 */
	public function getMetadata(string $type, string $version = BaseDefinition::DEFAULT_VERSION): array
	{
		// Ignore brackets (not supported by schema)
		$type = str_replace(['(', ')'], '', $type);

		// Normalize null type
		$type = str_replace('?', 'null|', $type);

		$usesUnionType = str_contains($type, '|');
		$usesIntersectionType = str_contains($type, '&');

		// Get schema for all possible types
		if ($usesUnionType || $usesIntersectionType) {
			$types = preg_split('#([&|])#', $type, -1, PREG_SPLIT_NO_EMPTY);

			if ($types === false) {
				throw new RuntimeException('Could not parse type ' . $type);
			}

			// Filter out duplicate definitions
			$types = array_map(fn (string $type): string => $this->normalizeType($type), $types);
			$types = array_unique($types);

			$nullKey = array_search('null', $types, true);
			$isNullable = $nullKey !== false;

			// Remove null from other types
			if ($nullKey !== false) {
				unset($types[$nullKey]);
			}

			// Types contain single, nullable value
			if (count($types) === 1) {
				$metadata = $this->getMetadata(current($types), $version);

				return $isNullable ? $this->applyNullable($metadata, $version) : $metadata;
			}

			$resolvedTypes = [];

			foreach ($types as $subType) {
				$resolvedTypes[] = $this->getMetadata($subType, $version);
			}

			if ($usesUnionType && $usesIntersectionType) {
				$schemaCombination = 'anyOf';
			} elseif ($usesUnionType) {
				$schemaCombination = 'oneOf';
			} else {
				$schemaCombination = 'allOf';
			}

			$metadata = [$schemaCombination => $resolvedTypes];

			return $isNullable ? $this->applyNullable($metadata, $version) : $metadata;
		}

		// Get schema for array
		if (str_ends_with($type, '[]')) {
			$subType = Strings::replace($type, '#\\[\\]#', '');

			return [
				'type' => 'array',
				'items' => $this->getMetadata($subType, $version),
			];
		}

		// Array shape
		if (preg_match('~array<(\w+),\s?([^>]+)>~', $type, $m)) {
			return [
				'type' => 'object',
				'additionalProperties' => $this->getMetadata($m[2], $version),
			];
		}

		// Get schema for class
		if (class_exists($type)) {
			// String is converted to DateTimeInterface internally in core
			if (is_subclass_of($type, DateTimeInterface::class)) {
				return [
					'type' => 'string',
					'format' => 'date-time',
				];
			}

			return [
				'type' => 'object',
				'properties' => $this->getProperties($type, $version),
			];
		}

		$lower = strtolower($type);

		// For php and phpstan is mixed absolutely anything, including null -> write in schema property accepts anything
		if ($lower === 'mixed') {
			return $this->applyNullable([], $version);
		}

		if ($lower === 'object' || interface_exists($type)) {
			return [
				'type' => 'object',
			];
		}

		// Get schema for scalar type
		return [
			'type' => $this->phpScalarTypeToOpenApiType($type),
		];
	}

	/**
	 * @return mixed[]
	 */
	protected function getProperties(string $type, string $version = BaseDefinition::DEFAULT_VERSION): array
	{
		if (!class_exists($type)) {
			return [];
		}

		$ref = new ReflectionClass($type);
		$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
		$data = [];

		foreach ($properties as $property) {
			$propertyType = $this->getPropertyType($property) ?? 'string';

			// Self-reference isn't supported
			if ($propertyType === $type) {
				$propertyType = 'object';
			}

			if (str_ends_with($propertyType, '[]')) {
				$subType = Strings::replace($propertyType, '#\\[\\]#', '');

				if ($subType === $type) {
					$propertyType = 'object';
				}
			}

			$data[$property->getName()] = $this->getMetadata($propertyType, $version);
		}

		return $data;
	}

	/**
	 * Converts scalar types (including phpdoc types and reserved words) to open api types
	 */
	protected function phpScalarTypeToOpenApiType(string $type): string
	{
		// Mixed and null not included, they are handled their own special way
		static $map = [
			'int' => 'integer',
			'float' => 'number',
			'bool' => 'boolean',
			'string' => 'string',
			'array' => 'array',
		];

		$type = $this->normalizeType($type);
		$lower = strtolower($type);

		if (!array_key_exists($lower, $map)) {
			throw new InvalidArgumentException(sprintf('Unsupported or unconvertible variable type \'%s\'', $type));
		}

		return $map[$lower];
	}

	protected function normalizeType(string $type): string
	{
		static $map = [
			'integer' => 'int',
			'double' => 'float',
			'numeric' => 'float',
			'boolean' => 'bool',
			'false' => 'bool',
			'true' => 'bool',
		];

		return $map[strtolower($type)] ?? $type;
	}

	private function getPropertyType(ReflectionProperty $property): ?string
	{
		$nativeType = null;

		if (($type = Type::fromReflection($property)) !== null) {
			$nativeType = $this->getNativePropertyType($type, $property);

			// If type is array/mixed or union/intersection of it, try to get more information from annotations
			if (!preg_match('#[|&]?(array|mixed)[|&]?#', $nativeType)) {
				return $nativeType;
			}
		}

		$annotation = $this->parseAnnotation($property, 'var');

		if ($annotation === null) {
			return $nativeType;
		}

		if (($type = preg_replace('#\s.*#', '', $annotation)) !== null) {
			$class = Reflection::getPropertyDeclaringClass($property);

			return preg_replace_callback('#[\w\\\\]+#', function ($m) use ($class): string {
				static $phpdocKnownTypes = [
					// phpcs:disable
					'bool', 'boolean', 'false', 'true',
					'int', 'integer',
					'float', 'double',
					'string', 'numeric', 'mixed', 'object',
					// phpcs:enable
				];

				$lower = $m[0];

				if (in_array($lower, $phpdocKnownTypes, true)) {
					return $this->normalizeType($lower);
				}

				// Self-reference not supported
				if (in_array($lower, ['static', 'self'], true)) {
					return 'object';
				}

				return Reflection::expandClassName($m[0], $class);
			}, $type);
		}

		return null;
	}

	/**
	 * @param ReflectionClass<object>|ReflectionClassConstant|ReflectionProperty|ReflectionFunctionAbstract $ref
	 */
	private function parseAnnotation(Reflector $ref, string $name): ?string
	{
		if (!Reflection::areCommentsAvailable()) {
			throw new InvalidStateException('You have to enable phpDoc comments in opcode cache.');
		}

		$re = '#[\s*]@' . preg_quote($name, '#') . '(?=\s|$)(?:[ \t]+([^@\s]\S*))?#';

		if ($ref->getDocComment() && preg_match($re, trim($ref->getDocComment(), '/*'), $m)) {
			return $m[1] ?? null;
		}

		return null;
	}

	private function getNativePropertyType(Type $type, ReflectionProperty $property): string
	{
		$names = array_map(strval(...), $type->getTypes());

		if ($type->isSimple() && count($names) === 1) {
			return $names[0];
		}

		if ($type->isUnion() || ($type->isSimple() && count($names) === 2) // nullable type is single but returns name of type and null in names
		) {
			return implode('|', $names);
		}

		if ($type->isIntersection()) {
			return implode('&', $names);
		}

		throw new RuntimeException(sprintf('Could not parse type "%s"', $property));
	}

	/**
	 * Marks a schema as accepting null, in the shape the target version understands.
	 * OpenAPI 3.1 dropped the "nullable" keyword in favour of JSON Schema 2020-12.
	 *
	 * @param mixed[] $schema
	 * @return mixed[]
	 */
	private function applyNullable(array $schema, string $version): array
	{
		if (!$this->isVersion31($version)) {
			// "nullable" stays the first key — generated documents are compared as-is
			return array_merge(['nullable' => true], $schema);
		}

		// An empty array would serialize as [], which is not a valid Schema Object.
		// Listing every type is the array-representable equivalent of accepting anything.
		if ($schema === []) {
			return [
				'type' => ['null', 'boolean', 'object', 'array', 'number', 'string'],
			];
		}

		if (isset($schema['type'])) {
			$types = is_array($schema['type']) ? $schema['type'] : [$schema['type']];

			if (!in_array('null', $types, true)) {
				$types[] = 'null';
			}

			// array_merge keeps "type" in its original position
			return array_merge($schema, ['type' => $types]);
		}

		foreach (['oneOf', 'anyOf'] as $combination) {
			if (isset($schema[$combination])) {
				$schema[$combination][] = ['type' => 'null'];

				return $schema;
			}
		}

		// Defensive: a schema without "type" and without a union of its own, which today
		// means allOf. Adding null into allOf would make it unsatisfiable, so it is wrapped.
		return ['anyOf' => [$schema, ['type' => 'null']]];
	}

	private function isVersion31(string $version): bool
	{
		return version_compare($version, '3.1', '>=');
	}

}
