<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi;

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\OpenApi\SchemaBuilder;
use Apitte\OpenApi\SchemaDefinition\ArrayDefinition;
use Apitte\OpenApi\SchemaDefinition\BaseDefinition;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\OpenApi\VersionAwareDefinitionMock;

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

	public function testVersionFromDefinitionReachesVersionAwareDefinition(): void
	{
		$builder = new SchemaBuilder();
		$builder->addDefinition(new BaseDefinition());
		$builder->addDefinition($versionAware = new VersionAwareDefinitionMock());
		$builder->addDefinition(new ArrayDefinition(['openapi' => '3.1.1']));

		$data = $builder->build()->toArray();

		Assert::same('3.1.1', $data['openapi']);
		Assert::same('3.1.1', $versionAware->receivedVersion);
	}

	public function testDefaultVersionWhenNoneDeclared(): void
	{
		$builder = new SchemaBuilder();
		$builder->addDefinition(new BaseDefinition());
		$builder->addDefinition($versionAware = new VersionAwareDefinitionMock());

		$data = $builder->build()->toArray();

		Assert::same(BaseDefinition::DEFAULT_VERSION, $data['openapi']);
		Assert::same(BaseDefinition::DEFAULT_VERSION, $versionAware->receivedVersion);
	}

	public function testMergeOrderIsPreserved(): void
	{
		$builder = new SchemaBuilder();
		$builder->addDefinition(new BaseDefinition());

		// The version-aware definition declares info.title...
		$builder->addDefinition(new VersionAwareDefinitionMock(['info' => ['title' => 'From core']]));

		// ...and a definition registered later must override it
		$builder->addDefinition(new ArrayDefinition(['info' => ['title' => 'From config', 'version' => '1.0.0']]));

		$data = $builder->build()->toArray();

		Assert::same('From config', $data['info']['title']);
	}

	public function testNonStringVersionThrows(): void
	{
		$builder = new SchemaBuilder();
		$builder->addDefinition(new BaseDefinition());
		$builder->addDefinition(new ArrayDefinition(['openapi' => 3.1]));

		Assert::exception(
			static fn () => $builder->build(),
			InvalidStateException::class,
			'OpenAPI version must be a string, float given. Quote the value in your configuration, e.g. openapi: \'3.1.1\'.'
		);
	}

}

(new SchemaBuilderTest())->run();
