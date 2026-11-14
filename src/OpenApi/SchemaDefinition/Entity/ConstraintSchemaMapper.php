<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition\Entity;

use ReflectionAttribute;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Describes Symfony validator constraints in OpenAPI schemas
 *
 * Requires symfony/validator, the entity adapter uses it only if the package is installed.
 */
class ConstraintSchemaMapper
{

	/**
	 * Applies Symfony validator constraints of the property to its schema (e.g. length limits, patterns and formats)
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
		switch (true) {
			case $constraint instanceof Assert\NotBlank:
				$schema['minLength'] = max(1, $schema['minLength'] ?? 1);

				break;
			case $constraint instanceof Assert\Length:
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
				if (is_int($constraint->min) || is_float($constraint->min)) {
					$schema['minimum'] = $constraint->min;
				}

				if (is_int($constraint->max) || is_float($constraint->max)) {
					$schema['maximum'] = $constraint->max;
				}

				break;
			case $constraint instanceof Assert\GreaterThanOrEqual:
				$schema['minimum'] = $constraint->value;

				break;
			case $constraint instanceof Assert\LessThanOrEqual:
				$schema['maximum'] = $constraint->value;

				break;
			case $constraint instanceof Assert\Unique:
				$schema['uniqueItems'] = true;

				break;
			case $constraint instanceof Assert\Choice:
				if (is_array($constraint->choices)) {
					$schema['enum'] = array_values($constraint->choices);
				}

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
				$schema['format'] = 'uri';

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
			case $constraint instanceof Assert\All:
				$items = is_array($schema['items'] ?? null) ? $schema['items'] : [];

				$itemConstraints = is_array($constraint->constraints) ? $constraint->constraints : [$constraint->constraints];

				foreach ($itemConstraints as $itemConstraint) {
					if ($itemConstraint instanceof Constraint) {
						$items = $this->applyConstraint($items, $itemConstraint);
					}
				}

				$schema['items'] = $items;

				break;
		}

		return $schema;
	}

	/**
	 * Converts the PCRE pattern to the ECMA-262 pattern used by JSON Schema (strips delimiters and modifiers)
	 */
	protected function convertPattern(string $pattern): string
	{
		if ($pattern === '') {
			return $pattern;
		}

		$end = strrpos($pattern, $pattern[0]);

		return $end === false || $end === 0 ? $pattern : substr($pattern, 1, $end - 1);
	}

}
