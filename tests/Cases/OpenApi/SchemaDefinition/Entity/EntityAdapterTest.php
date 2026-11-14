<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\SchemaDefinition\Entity;

use Apitte\OpenApi\SchemaDefinition\Entity\EntityAdapter;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\ResponseEntity\ArrayShapeEntity;
use Tests\Fixtures\ResponseEntity\CompoundResponseEntity;
use Tests\Fixtures\ResponseEntity\ConstrainedEntity;
use Tests\Fixtures\ResponseEntity\EmptyResponseEntity;
use Tests\Fixtures\ResponseEntity\EnumEntity;
use Tests\Fixtures\ResponseEntity\ExtendedConstrainedEntity;
use Tests\Fixtures\ResponseEntity\MixedEntity;
use Tests\Fixtures\ResponseEntity\NativeIntersectionEntity;
use Tests\Fixtures\ResponseEntity\NativeUnionEntity;
use Tests\Fixtures\ResponseEntity\NullableCompoundEntity;
use Tests\Fixtures\ResponseEntity\SelfReferencingEntity;
use Tests\Fixtures\ResponseEntity\StringBackedEnum;
use Tests\Fixtures\ResponseEntity\TypedResponseEntity;

require_once __DIR__ . '/../../../../bootstrap.php';

final class EntityAdapterTest extends TestCase
{

	public function testComplex(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'anyOf' => [
					['type' => 'integer'],
					['type' => 'number'],
					[
						'type' => 'object',
						'properties' => [
							'nullableObjects' => [
								'nullable' => true,
								'type' => 'array',
								'items' => [
									'type' => 'object',
									'properties' => [
										'int' => ['type' => 'integer'],
										'nullableFloat' => ['nullable' => true, 'type' => 'number'],
										'string' => ['type' => 'string'],
										'bool' => ['type' => 'boolean'],
										'datetime' => ['type' => 'string', 'format' => 'date-time'],
										'mixed' => ['nullable' => true],
										'untypedProperty' => ['type' => 'string'],
									],
								],
							],
							'unionProperties' => [
								'oneOf' => [
									['type' => 'array', 'items' => ['type' => 'string']],
									['type' => 'array', 'items' => ['type' => 'integer']],
								],
							],
							'intersectionProperties' => [
								'allOf' => [
									['type' => 'array', 'items' => ['type' => 'string']],
									['type' => 'array', 'items' => ['type' => 'integer']],
								],
							],
							'unionAndIntersectionProperties' => [
								'anyOf' => [
									['type' => 'array', 'items' => ['type' => 'string']],
									['type' => 'array', 'items' => ['type' => 'integer']],
									['type' => 'array', 'items' => ['type' => 'number']],
								],
							],
						],
					],
				],
			],
			$adapter->getMetadata('(int&float)|' . CompoundResponseEntity::class)
		);
	}

	public function testAlternativeSyntax(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['oneOf' => [['type' => 'integer'], ['type' => 'number'], ['type' => 'boolean']]],
			$adapter->getMetadata('integer|double|boolean')
		);
	}

	public function testDuplicates(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['oneOf' => [['type' => 'boolean'], ['type' => 'number']]],
			$adapter->getMetadata('bool|boolean|true|false|double|float|numeric')
		);

		Assert::same(
			['type' => 'boolean'],
			$adapter->getMetadata('bool|boolean|true|false')
		);
	}

	public function testObject(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => 'object'],
			$adapter->getMetadata('object')
		);

		Assert::same(
			['type' => 'object'],
			$adapter->getMetadata(DateTimeInterface::class)
		);
	}

	public function testMixed(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['nullable' => true],
			$adapter->getMetadata('mixed')
		);
	}

	public function testDateTime(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => 'string', 'format' => 'date-time'],
			$adapter->getMetadata(DateTimeImmutable::class)
		);

		Assert::same(
			['type' => 'string', 'format' => 'date-time'],
			$adapter->getMetadata(DateTime::class)
		);
	}

	public function testArray(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => 'array', 'items' => ['type' => 'string']],
			$adapter->getMetadata('string[]')
		);
	}

	public function testSelfReference(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'selfReference' => ['type' => 'object'],
					'staticReference' => ['type' => 'object'],
					'classNameReference' => ['type' => 'object'],
					'normalProperty' => ['type' => 'string'],
				],
			],
			$adapter->getMetadata(SelfReferencingEntity::class)
		);
	}

	public function testBadCase(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['oneOf' => [['type' => 'boolean'], ['type' => 'string']]],
			$adapter->getMetadata('bOOl|STRing')
		);
	}

	/**
	 * @phpVersion 7.4
	 */
	public function testTypedEntity(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'int' => ['type' => 'integer'],
					'nullableFloat' => ['nullable' => true, 'type' => 'number'],
					'string' => ['type' => 'string'],
					'bool' => ['type' => 'boolean'],
					'datetime' => ['type' => 'string', 'format' => 'date-time'],
					'phpdocInt' => ['type' => 'integer'],
					'untypedProperty' => ['type' => 'string'],
					'untypedArray' => ['type' => 'array'],
					'arrayOfInt' => ['type' => 'array', 'items' => ['type' => 'integer']],
				],
			],
			$adapter->getMetadata(TypedResponseEntity::class)
		);
	}

	public function testArrayShape(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'shapeOfStringToInt' => [
						'type' => 'object',
						'additionalProperties' => [
							'type' => 'integer',
						],
					],
					'shapeOfStringToObject' => [
						'type' => 'object',
						'additionalProperties' => [
							'type' => 'object',
							'properties' => [
								'name' => [
									'type' => 'string',
								],
							],
						],
					],
				],
			],
			$adapter->getMetadata(ArrayShapeEntity::class)
		);
	}

	public function testNativeUnionEntity(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'stringOrInt' => [
						'oneOf' => [
							['type' => 'string'],
							['type' => 'integer'],
						],
					],
					'nullableBool' => ['nullable' => true, 'type' => 'boolean'],
					'dateOrInt' => [
						'oneOf' => [
							['type' => 'string', 'format' => 'date-time'],
							['type' => 'integer'],
						],
					],
					'arrayOrInt' => [
						'oneOf' => [
							['type' => 'array'],
							['type' => 'integer'],
						],
					],
					'strings' => [
						'oneOf' => [
							['type' => 'array', 'items' => ['type' => 'string']],
							['type' => 'string'],
						],
					],
				],
			],
			$adapter->getMetadata(NativeUnionEntity::class)
		);
	}

	public function testNativeIntersectionEntity(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'intersection' => [
						'allOf' => [
							['type' => 'string', 'format' => 'date-time'],
							['type' => 'object'],
						],
					],
				],
			],
			$adapter->getMetadata(NativeIntersectionEntity::class)
		);
	}

	public function testMixedEntity(): void
	{
		$adapter = new EntityAdapter();
		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'mixed' => ['nullable' => true],
					'complexType' => [
						'anyOf' => [
							['type' => 'array', 'items' => ['type' => 'string']],
							['type' => 'array', 'items' => ['type' => 'integer']],
							['type' => 'array', 'items' => ['type' => 'number']],
						],
					],
				],
			],
			$adapter->getMetadata(MixedEntity::class)
		);
	}

	public function testNullableScalarIn31(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => ['string', 'null']],
			$adapter->getMetadata('?string', '3.1.1')
		);

		Assert::same(
			['type' => ['number', 'null']],
			$adapter->getMetadata('float|null', '3.1.1')
		);
	}

	public function testNullableArrayAndObjectIn31(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => ['array', 'null'], 'items' => ['type' => 'string']],
			$adapter->getMetadata('string[]|null', '3.1.1')
		);

		Assert::same(
			['type' => ['object', 'null'], 'properties' => []],
			$adapter->getMetadata('?' . EmptyResponseEntity::class, '3.1.1')
		);
	}

	public function testMixedIn31(): void
	{
		$adapter = new EntityAdapter();

		// An empty array would serialize as [], which is not a valid Schema Object.
		// Enumerating all the types is functionally equivalent to a schema accepting anything.
		Assert::same(
			['type' => ['null', 'boolean', 'object', 'array', 'number', 'string']],
			$adapter->getMetadata('mixed', '3.1.1')
		);
	}

	public function testNonNullableSchemaIsSameIn31(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => 'string'],
			$adapter->getMetadata('string', '3.1.1')
		);

		Assert::same(
			['oneOf' => [['type' => 'integer'], ['type' => 'number']]],
			$adapter->getMetadata('int|float', '3.1.1')
		);

		Assert::same(
			['allOf' => [['type' => 'string', 'format' => 'date-time'], ['type' => 'object']]],
			$adapter->getMetadata('DateTime&ArrayAccess', '3.1.1')
		);
	}

	public function testNullableCombinatorsIn30(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'nullableUnion' => [
						'nullable' => true,
						'oneOf' => [
							['type' => 'array', 'items' => ['type' => 'string']],
							['type' => 'array', 'items' => ['type' => 'integer']],
						],
					],
					'nullableIntersection' => [
						'nullable' => true,
						'anyOf' => [
							['type' => 'string', 'format' => 'date-time'],
							['type' => 'object'],
						],
					],
				],
			],
			$adapter->getMetadata(NullableCompoundEntity::class)
		);
	}

	public function testNullableCombinatorsIn31(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'nullableUnion' => [
						'oneOf' => [
							['type' => 'array', 'items' => ['type' => 'string']],
							['type' => 'array', 'items' => ['type' => 'integer']],
							['type' => 'null'],
						],
					],
					'nullableIntersection' => [
						'anyOf' => [
							['type' => 'string', 'format' => 'date-time'],
							['type' => 'object'],
							['type' => 'null'],
						],
					],
				],
			],
			$adapter->getMetadata(NullableCompoundEntity::class, '3.1.1')
		);
	}

	public function testMixedIn31SerializesAsObject(): void
	{
		$adapter = new EntityAdapter();

		$schema = $adapter->getMetadata('mixed', '3.1.1');

		// A Schema Object must not be an array — [] would be rejected by every validator
		Assert::notSame('[]', json_encode($schema));
		Assert::contains('"type"', (string) json_encode($schema));
	}

	public function testEnum(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			['type' => 'string', 'enum' => ['foo', 'bar']],
			$adapter->getMetadata(StringBackedEnum::class),
		);

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'string' => ['type' => 'string', 'enum' => ['foo', 'bar']],
					'int' => ['type' => 'integer', 'enum' => [1, 2]],
					'pure' => ['type' => 'string', 'enum' => ['Alpha', 'Beta']],
					'nullable' => ['nullable' => true, 'type' => 'string', 'enum' => ['foo', 'bar']],
				],
			],
			$adapter->getMetadata(EnumEntity::class),
		);
	}

	public function testConstraints(): void
	{
		$adapter = new EntityAdapter();

		Assert::same(
			[
				'type' => 'object',
				'properties' => [
					'name' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 255],
					'password' => ['type' => 'string', 'minLength' => 8, 'maxLength' => 64],
					'token' => ['type' => 'string', 'pattern' => '^[A-Za-z0-9_-]+$'],
					'email' => ['nullable' => true, 'type' => 'string', 'format' => 'email'],
					'url' => ['type' => 'string', 'format' => 'uri'],
					'uuid' => ['type' => 'string', 'format' => 'uuid'],
					'hostname' => ['type' => 'string', 'format' => 'hostname'],
					'ipv6' => ['type' => 'string', 'format' => 'ipv6'],
					'ip' => ['type' => 'string', 'anyOf' => [['format' => 'ipv4'], ['format' => 'ipv6']]],
					'port' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 65535],
					'percent' => ['type' => 'number', 'minimum' => 0, 'maximum' => 100],
					'secure' => ['type' => 'string', 'enum' => ['ssl', 'tls']],
					'names' => [
						'type' => 'array',
						'items' => ['type' => 'string', 'maxLength' => 64, 'format' => 'hostname'],
						'minItems' => 1,
						'maxItems' => 10,
						'uniqueItems' => true,
					],
				],
			],
			$adapter->getMetadata(ConstrainedEntity::class),
		);
	}

	public function testExtendedConstraints(): void
	{
		$adapter = new EntityAdapter();
		$properties = $adapter->getMetadata(ExtendedConstrainedEntity::class)['properties'];

		// Empty Schema Objects are serialized as JSON objects, they cannot be compared by identity
		Assert::equal(
			[
				'type' => 'object',
				'properties' => [
					'host' => ['minLength' => 1, 'format' => 'hostname'],
					'port' => ['minimum' => 1, 'maximum' => 65535],
					'tag' => (object) [],
				],
				'required' => ['host'],
				'additionalProperties' => false,
			],
			$properties['server'],
		);
		unset($properties['server']);

		Assert::same(
			[
				'positive' => ['type' => 'integer', 'minimum' => 0, 'exclusiveMinimum' => true],
				'negativeOrZero' => ['type' => 'integer', 'maximum' => 0],
				'exclusive' => ['type' => 'number', 'minimum' => 1.5, 'exclusiveMinimum' => true, 'maximum' => 10, 'exclusiveMaximum' => true],
				'divisible' => ['type' => 'integer', 'multipleOf' => 5],
				'date' => ['type' => 'string'],
				'version' => ['type' => 'string', 'enum' => ['v1']],
				'username' => ['type' => 'string', 'not' => ['enum' => ['admin']]],
				'agreed' => ['type' => 'boolean', 'enum' => [true]],
				'birthday' => ['type' => 'string', 'format' => 'date'],
				'relativeUrl' => ['type' => 'string', 'format' => 'uri-reference'],
				'ulid' => ['type' => 'string', 'pattern' => '^[0-7][0-9A-HJKMNP-TV-Z]{25}$'],
				'week' => ['type' => 'string', 'pattern' => '^\\d{4}-W\\d{2}$'],
				'permissions' => [
					'type' => 'array',
					'items' => ['type' => 'string', 'enum' => ['read', 'write']],
					'minItems' => 1,
					'maxItems' => 2,
				],
				'role' => ['type' => 'string'],
				'uniqueById' => ['type' => 'array', 'items' => ['nullable' => true]],
				'notEmpty' => ['type' => 'array', 'minItems' => 1],
				'bytes' => ['type' => 'string'],
				'integers' => ['type' => 'array', 'items' => ['type' => 'integer', 'nullable' => true]],
				'contact' => ['type' => 'string', 'anyOf' => [['format' => 'email'], ['format' => 'ipv4']]],
				'indescribable' => ['type' => 'string'],
				'sequential' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 32],
				'grouped' => ['type' => 'string'],
				'callback' => ['type' => 'string'],
			],
			$properties,
		);
	}

	public function testExclusiveLimitsOpenApi31(): void
	{
		$properties = (new EntityAdapter())->getMetadata(ExtendedConstrainedEntity::class, '3.1.0')['properties'];

		Assert::same(['type' => 'integer', 'exclusiveMinimum' => 0], $properties['positive']);
		Assert::same(['type' => 'number', 'exclusiveMinimum' => 1.5, 'exclusiveMaximum' => 10], $properties['exclusive']);
	}

	public function testValidationGroups(): void
	{
		$properties = (new EntityAdapter(['strict']))->getMetadata(ExtendedConstrainedEntity::class)['properties'];

		Assert::same(['type' => 'string', 'maxLength' => 5], $properties['grouped']);
		Assert::same(['type' => 'integer'], $properties['positive']);
	}

}

(new EntityAdapterTest())->run();
