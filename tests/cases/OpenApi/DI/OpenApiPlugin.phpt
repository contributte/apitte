<?php declare(strict_types = 1);

/**
 * Test: DI\OpenApiPlugin
 */

use Apitte\Core\DI\ApiExtension;
use Apitte\OpenApi\DI\OpenApiPlugin;
use Apitte\OpenApi\ISchemaBuilder;
use Apitte\OpenApi\SchemaBuilder;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Default
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'api' => [
				'plugins' => [
					OpenApiPlugin::class => [
						'definition' => [
							'openapi' => '3.0.2',
							'info' => [
								'title' => 'Swagger Petstore',
								'version' => '1.0.0',
							],
							'paths' => [],
						],
					],
				],
			],
		]);
	}, 1);

	/** @var Container $container */
	$container = new $class();

	/** @var SchemaBuilder $schemaBuilder */
	$schemaBuilder = $container->getByType(ISchemaBuilder::class);
	Assert::type(ISchemaBuilder::class, $schemaBuilder);
	Assert::equal([
		'openapi' => '3.0.2',
		'info' => ['title' => 'Swagger Petstore', 'version' => '1.0.0'],
		'paths' => [],
	], $schemaBuilder->build()->toArray());
});
