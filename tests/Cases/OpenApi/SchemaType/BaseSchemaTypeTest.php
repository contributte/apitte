<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\SchemaType;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Schema\EndpointParameter;
use Apitte\OpenApi\Schema\Schema;
use Apitte\OpenApi\SchemaType\BaseSchemaType;
use Apitte\OpenApi\SchemaType\ISchemaType;
use Tester\Assert;
use Tester\TestCase;

final class BaseSchemaTypeTest extends TestCase
{

	private ISchemaType $baseSchemaType;

	public function testString(): void
	{
		$endpointParameter = new EndpointParameter('foo', EndpointParameter::TYPE_STRING);

		$scalarSchema = $this->baseSchemaType->createSchema($endpointParameter);

		Assert::type(Schema::class, $scalarSchema);
		Assert::same(
			[
				'type' => 'string',
			],
			$scalarSchema->toArray()
		);
	}

	public function testInteger(): void
	{
		$endpointParameter = new EndpointParameter('foo', EndpointParameter::TYPE_INTEGER);

		$scalarSchema = $this->baseSchemaType->createSchema($endpointParameter);

		Assert::type(Schema::class, $scalarSchema);
		Assert::same(
			[
				'type' => 'integer',
				'format' => 'int32',
			],
			$scalarSchema->toArray()
		);
	}

	public function testFloat(): void
	{
		$endpointParameter = new EndpointParameter('foo', EndpointParameter::TYPE_FLOAT);

		$scalarSchema = $this->baseSchemaType->createSchema($endpointParameter);

		Assert::type(Schema::class, $scalarSchema);
		Assert::same(
			[
				'type' => 'float',
				'format' => 'float64',
			],
			$scalarSchema->toArray()
		);
	}

	public function testBoolean(): void
	{
		$endpointParameter = new EndpointParameter('foo', EndpointParameter::TYPE_BOOLEAN);

		$scalarSchema = $this->baseSchemaType->createSchema($endpointParameter);

		Assert::type(Schema::class, $scalarSchema);
		Assert::same(
			[
				'type' => 'boolean',
			],
			$scalarSchema->toArray()
		);
	}

	public function testDatetime(): void
	{
		$endpointParameter = new EndpointParameter('foo', EndpointParameter::TYPE_DATETIME);

		$scalarSchema = $this->baseSchemaType->createSchema($endpointParameter);

		Assert::type(Schema::class, $scalarSchema);
		Assert::same(
			[
				'type' => 'string',
				'format' => 'date-time',
			],
			$scalarSchema->toArray()
		);
	}

	protected function setUp(): void
	{
		$this->baseSchemaType = new BaseSchemaType();
	}

}

(new BaseSchemaTypeTest())->run();
