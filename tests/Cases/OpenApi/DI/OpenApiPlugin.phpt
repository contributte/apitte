<?php declare(strict_types = 1);

use Apitte\Core\DI\ApiExtension;
use Apitte\OpenApi\DI\OpenApiPlugin;
use Apitte\OpenApi\ISchemaBuilder;
use Apitte\OpenApi\SchemaBuilder;
use Apitte\OpenApi\SchemaType\BaseSchemaType;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Default
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
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
						'swaggerUi' => [
							'panel' => true,
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

// Schema type is registered as a service of the expected type
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => false,
			],
			'api' => [
				'plugins' => [
					OpenApiPlugin::class => [],
				],
			],
		]);
	}, 2);

	/** @var Container $container */
	$container = new $class();

	Assert::type(BaseSchemaType::class, $container->getService('api.openapi.schemaType'));
});

// Version declared in the definition reaches the built document
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => false,
			],
			'api' => [
				'plugins' => [
					OpenApiPlugin::class => [
						'definition' => [
							'openapi' => '3.1.1',
							'info' => [
								'title' => 'Nullable demo',
								'version' => '1.0.0',
							],
						],
					],
				],
			],
		]);
	}, 3);

	/** @var Container $container */
	$container = new $class();

	/** @var SchemaBuilder $schemaBuilder */
	$schemaBuilder = $container->getByType(ISchemaBuilder::class);
	Assert::same('3.1.1', $schemaBuilder->build()->toArray()['openapi']);
});
