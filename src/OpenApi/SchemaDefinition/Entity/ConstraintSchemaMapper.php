<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition\Entity;

use Apitte\OpenApi\SchemaDefinition\BaseDefinition;
use ReflectionAttribute;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Describes Symfony validator constraints in OpenAPI schemas
 *
 * Requires symfony/validator (e.g. via contributte/validator), the entity adapter uses it only if the package is installed.
 * Constraints which cannot be expressed by a schema (e.g. Callback, Expression or checksums) are skipped,
 * so the schema never rejects a value accepted by the validator.
 */
class ConstraintSchemaMapper
{

	/**
	 * @param list<string> $groups Validation groups, constraints of other groups are skipped
	 */
	public function __construct(
		private string $version = BaseDefinition::DEFAULT_VERSION,
		private array $groups = [Constraint::DEFAULT_GROUP],
	)
	{
	}

	/**
	 * Applies Symfony validator constraints of the property to its schema
	 *
	 * @param mixed[] $schema
	 * @return mixed[]
	 */
	public function apply(array $schema, ReflectionProperty $property): array
	{
		foreach ($property->getAttributes(Constraint::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
			$schema = $this->applyConstraint($schema, $attribute->newInstance());
		}

		return $schema;
	}

	/**
	 * @param mixed[] $schema
	 * @return mixed[]
	 */
	public function applyConstraint(array $schema, Constraint $constraint): array
	{
		if (!$this->isInGroups($constraint)) {
			return $schema;
		}

		switch (true) {
			case $constraint instanceof Assert\NotBlank:
				if ($this->hasType($schema, 'array')) {
					$schema['minItems'] = max(1, $schema['minItems'] ?? 1);
				} else {
					$schema['minLength'] = max(1, $schema['minLength'] ?? 1);
				}

				break;
			case $constraint instanceof Assert\Length:
				// JSON Schema counts code points, lengths counted in bytes or graphemes cannot be described
				if ($constraint->countUnit !== Assert\Length::COUNT_CODEPOINTS) {
					break;
				}

				if ($constraint->min !== null && $constraint->min > 0) {
					$schema['minLength'] = $constraint->min;
				}

				if ($constraint->max !== null) {
					$schema['maxLength'] = $constraint->max;
				}

				break;
			case $constraint instanceof Assert\Count:
				if ($constraint->min !== null) {
					$schema['minItems'] = $constraint->min;
				}

				if ($constraint->max !== null) {
					$schema['maxItems'] = $constraint->max;
				}

				break;
			case $constraint instanceof Assert\Range:
				if ($constraint->minPropertyPath === null && $this->isNumber($constraint->min)) {
					$schema['minimum'] = $constraint->min;
				}

				if ($constraint->maxPropertyPath === null && $this->isNumber($constraint->max)) {
					$schema['maximum'] = $constraint->max;
				}

				break;
			// Including Positive (greater than 0)
			case $constraint instanceof Assert\GreaterThan:
				if ($this->isNumericComparison($constraint)) {
					$schema = $this->applyExclusiveLimit($schema, 'minimum', $constraint->value);
				}

				break;
			// Including Negative (less than 0)
			case $constraint instanceof Assert\LessThan:
				if ($this->isNumericComparison($constraint)) {
					$schema = $this->applyExclusiveLimit($schema, 'maximum', $constraint->value);
				}

				break;
			// Including PositiveOrZero
			case $constraint instanceof Assert\GreaterThanOrEqual:
				if ($this->isNumericComparison($constraint)) {
					$schema['minimum'] = $constraint->value;
				}

				break;
			// Including NegativeOrZero
			case $constraint instanceof Assert\LessThanOrEqual:
				if ($this->isNumericComparison($constraint)) {
					$schema['maximum'] = $constraint->value;
				}

				break;
			case $constraint instanceof Assert\DivisibleBy:
				if ($this->isNumericComparison($constraint) && $constraint->value > 0) {
					$schema['multipleOf'] = $constraint->value;
				}

				break;
			case $constraint instanceof Assert\EqualTo:
			case $constraint instanceof Assert\IdenticalTo:
				if ($this->isScalarComparison($constraint)) {
					$schema['enum'] = [$constraint->value];
				}

				break;
			case $constraint instanceof Assert\NotEqualTo:
			case $constraint instanceof Assert\NotIdenticalTo:
				if ($this->isScalarComparison($constraint)) {
					$schema['not'] = ['enum' => [$constraint->value]];
				}

				break;
			case $constraint instanceof Assert\IsTrue:
				$schema['enum'] = [true];

				break;
			case $constraint instanceof Assert\IsFalse:
				$schema['enum'] = [false];

				break;
			case $constraint instanceof Assert\IsNull:
				$schema['enum'] = [null];

				break;
			case $constraint instanceof Assert\Type:
				$schema = $this->applyType($schema, (array) $constraint->type);

				break;
			case $constraint instanceof Assert\Unique:
				// Uniqueness of selected fields or normalized values differs from uniqueItems
				if ($constraint->fields === [] && $constraint->normalizer === null) {
					$schema['uniqueItems'] = true;
				}

				break;
			case $constraint instanceof Assert\Choice:
				$schema = $this->applyChoice($schema, $constraint);

				break;
			case $constraint instanceof Assert\Regex:
				if ($constraint->match && $constraint->pattern !== null) {
					$schema['pattern'] = $this->convertPattern($constraint->pattern);
				}

				break;
			case $constraint instanceof Assert\Email:
				$schema['format'] = 'email';

				break;
			case $constraint instanceof Assert\Url:
				$schema['format'] = $constraint->relativeProtocol ? 'uri-reference' : 'uri';

				break;
			case $constraint instanceof Assert\Uuid:
				$schema['format'] = 'uuid';

				break;
			case $constraint instanceof Assert\Hostname:
				$schema['format'] = 'hostname';

				break;
			case $constraint instanceof Assert\Ip:
				if (str_starts_with($constraint->version, Assert\Ip::V4)) {
					$schema['format'] = 'ipv4';
				} elseif (str_starts_with($constraint->version, Assert\Ip::V6)) {
					$schema['format'] = 'ipv6';
				} else {
					$schema['anyOf'] = [['format' => 'ipv4'], ['format' => 'ipv6']];
				}

				break;
			case $constraint instanceof Assert\Date:
				$schema['format'] = 'date';

				break;
			case $constraint instanceof Assert\Ulid:
				if ($constraint->format === Assert\Ulid::FORMAT_BASE_32) {
					$schema['pattern'] = '^[0-7][0-9A-HJKMNP-TV-Z]{25}$';
				}

				break;
			case $constraint instanceof Assert\MacAddress:
				$schema['pattern'] = '^(?:[0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}$|^[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}$';

				break;
			case $constraint instanceof Assert\Week:
				$schema['pattern'] = '^\d{4}-W\d{2}$';

				break;
			case $constraint instanceof Assert\Isin:
				$schema['pattern'] = '^[A-Z]{2}[A-Z0-9]{9}[0-9]$';

				break;
			case $constraint instanceof Assert\Country:
				$schema['pattern'] = $constraint->alpha3 ? '^[A-Z]{3}$' : '^[A-Z]{2}$';

				break;
			case $constraint instanceof Assert\Currency:
				$schema['pattern'] = '^[A-Z]{3}$';

				break;
			case $constraint instanceof Assert\All:
				$items = is_array($schema['items'] ?? null) ? $schema['items'] : [];

				foreach ($this->getNestedConstraints($constraint) as $itemConstraint) {
					$items = $this->applyConstraint($items, $itemConstraint);
				}

				$schema['items'] = $items;

				break;
			case $constraint instanceof Assert\Collection:
				$schema = $this->applyCollection($schema, $constraint);

				break;
			case $constraint instanceof Assert\AtLeastOneOf:
				$schema = $this->applyAtLeastOneOf($schema, $constraint);

				break;
			case $constraint instanceof Assert\Sequentially:
			case $constraint instanceof Assert\Compound:
				foreach ($this->getNestedConstraints($constraint) as $nestedConstraint) {
					$schema = $this->applyConstraint($schema, $nestedConstraint);
				}

				break;
		}

		return $schema;
	}

	/**
	 * Converts the PCRE pattern to the ECMA-262 pattern used by JSON Schema (strips delimiters and modifiers)
	 */
	public function convertPattern(string $pattern): string
	{
		if ($pattern === '') {
			return $pattern;
		}

		$end = strrpos($pattern, $pattern[0]);

		return $end === false || $end === 0 ? $pattern : substr($pattern, 1, $end - 1);
	}

	/**
	 * Exclusive limits are boolean flags in OpenAPI 3.0 and numbers in OpenAPI 3.1
	 *
	 * @param mixed[] $schema
	 * @param 'minimum'|'maximum' $limit
	 * @return mixed[]
	 */
	private function applyExclusiveLimit(array $schema, string $limit, int|float $value): array
	{
		$keyword = 'exclusive' . ucfirst($limit);

		if (version_compare($this->version, '3.1.0', '>=')) {
			$schema[$keyword] = $value;
		} else {
			$schema[$limit] = $value;
			$schema[$keyword] = true;
		}

		return $schema;
	}

	/**
	 * Completes the type of untyped schemas (e.g. array items)
	 *
	 * @param mixed[] $schema
	 * @param array<string> $types
	 * @return mixed[]
	 */
	private function applyType(array $schema, array $types): array
	{
		static $map = [
			'string' => 'string',
			'int' => 'integer',
			'integer' => 'integer',
			'long' => 'integer',
			'float' => 'number',
			'double' => 'number',
			'real' => 'number',
			'numeric' => 'number',
			'bool' => 'boolean',
			'boolean' => 'boolean',
			'array' => 'array',
			'list' => 'array',
		];

		// Type of the property is already described by its PHP type
		if (isset($schema['type'])) {
			return $schema;
		}

		$mapped = [];

		foreach ($types as $type) {
			$type = strtolower($type);

			// Types without JSON Schema counterpart (e.g. classes or ctype_* functions) cannot be described
			if (!isset($map[$type])) {
				return $schema;
			}

			$mapped[] = $map[$type];
		}

		$mapped = array_values(array_unique($mapped));

		if ($mapped === []) {
			return $schema;
		}

		if (count($mapped) === 1) {
			return ['type' => $mapped[0]] + $schema;
		}

		$schema['anyOf'] = array_map(static fn (string $type): array => ['type' => $type], $mapped);

		return $schema;
	}

	/**
	 * @param mixed[] $schema
	 * @return mixed[]
	 */
	private function applyChoice(array $schema, Assert\Choice $constraint): array
	{
		// Choices provided by a callback are unknown and excluded choices cannot be listed
		if (!$constraint->match || !is_array($constraint->choices)) {
			return $schema;
		}

		$choices = array_values($constraint->choices);

		if (!$constraint->multiple) {
			$schema['enum'] = $choices;

			return $schema;
		}

		$items = is_array($schema['items'] ?? null) ? $schema['items'] : [];
		$items['enum'] = $choices;
		$schema['items'] = $items;

		if ($constraint->min !== null) {
			$schema['minItems'] = $constraint->min;
		}

		if ($constraint->max !== null) {
			$schema['maxItems'] = $constraint->max;
		}

		return $schema;
	}

	/**
	 * @param mixed[] $schema
	 * @return mixed[]
	 */
	private function applyCollection(array $schema, Assert\Collection $constraint): array
	{
		$properties = [];
		$required = [];

		foreach ($constraint->fields as $name => $field) {
			$fieldSchema = [];

			foreach ($this->getNestedConstraints($field) as $fieldConstraint) {
				$fieldSchema = $this->applyConstraint($fieldSchema, $fieldConstraint);
			}

			// An empty array would be serialized as a JSON array instead of an empty Schema Object
			$properties[(string) $name] = $fieldSchema === [] ? (object) [] : $fieldSchema;

			if (!$constraint->allowMissingFields && !$field instanceof Assert\Optional) {
				$required[] = (string) $name;
			}
		}

		$schema['type'] = 'object';
		$schema['properties'] = $properties;

		if ($required !== []) {
			$schema['required'] = $required;
		}

		if (!$constraint->allowExtraFields) {
			$schema['additionalProperties'] = false;
		}

		return $schema;
	}

	/**
	 * @param mixed[] $schema
	 * @return mixed[]
	 */
	private function applyAtLeastOneOf(array $schema, Assert\AtLeastOneOf $constraint): array
	{
		$alternatives = [];

		foreach ($this->getNestedConstraints($constraint) as $nestedConstraint) {
			$alternative = $this->applyConstraint([], $nestedConstraint);

			// An indescribable alternative accepts anything, so the whole constraint cannot be described
			if ($alternative === []) {
				return $schema;
			}

			$alternatives[] = $alternative;
		}

		if ($alternatives !== []) {
			$schema['anyOf'] = $alternatives;
		}

		return $schema;
	}

	/**
	 * @return list<Constraint>
	 */
	private function getNestedConstraints(Constraint $constraint): array
	{
		$nested = property_exists($constraint, 'constraints') ? $constraint->constraints : [];
		$nested = is_array($nested) ? $nested : [$nested];

		return array_values(array_filter($nested, static fn (mixed $item): bool => $item instanceof Constraint));
	}

	private function isInGroups(Constraint $constraint): bool
	{
		return array_intersect($constraint->groups ?? [Constraint::DEFAULT_GROUP], $this->groups) !== [];
	}

	private function isNumericComparison(Assert\AbstractComparison $constraint): bool
	{
		return $constraint->propertyPath === null && $this->isNumber($constraint->value);
	}

	private function isScalarComparison(Assert\AbstractComparison $constraint): bool
	{
		return $constraint->propertyPath === null && (is_scalar($constraint->value) || $constraint->value === null);
	}

	/**
	 * @phpstan-assert-if-true int|float $value
	 */
	private function isNumber(mixed $value): bool
	{
		return is_int($value) || is_float($value);
	}

	/**
	 * @param mixed[] $schema
	 */
	private function hasType(array $schema, string $type): bool
	{
		$types = $schema['type'] ?? [];

		return in_array($type, is_array($types) ? $types : [$types], true);
	}

}
