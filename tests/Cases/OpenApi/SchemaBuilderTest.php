<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi;

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\OpenApi\SchemaBuilder;
use Apitte\OpenApi\SchemaDefinition\ArrayDefinition;
use Tester\Assert;
use Tester\TestCase;

final class SchemaBuilderTest extends TestCase
{

	public function testBuild(): void
	{
		$a = [
			'openapi' => '3.0.2',
			'paths' => [],
		];
		$b = [
			'info' => [
				'title' => 'Petstore',
				'version' => '1.0.0',
			],
			'servers' => [
				[
					'url' => 'www.example.com',
				],
			],
		];
		$c = [
			'info' => [
				'version' => '1.0.2',
				'description' => 'Hello world',
			],
			'servers' => [
				[
					'url' => 'www.example2.com',
				],
			],
			'tags' => [
				[
					'name' => 'Pet',
				],
			],
		];

		$schemaBuilder = new SchemaBuilder();
		$schemaBuilder->addDefinition(new ArrayDefinition($a));
		$schemaBuilder->addDefinition(new ArrayDefinition($b));
		$schemaBuilder->addDefinition(new ArrayDefinition($c));
		$schema = $schemaBuilder->build();

		Assert::same(
			[
				'openapi' => '3.0.2',
				'info' => [
					'title' => 'Petstore',
					'description' => 'Hello world',
					'version' => '1.0.2',
				],
				'servers' => [
					[
						'url' => 'www.example.com',
					],
					[
						'url' => 'www.example2.com',
					],
				],
				'paths' => [],
				'tags' => [
					[
						'name' => 'Pet',
					],
				],
			],
			$schema->toArray()
		);
	}

}

(new SchemaBuilderTest())->run();
