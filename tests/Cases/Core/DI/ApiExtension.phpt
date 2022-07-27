<?php declare(strict_types = 1);

/**
 * Test: DI\ApiExtension
 *
 * @phpVersion > 7.3
 */

use Apitte\Core\Application\Application;
use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Dispatcher\JsonDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\ErrorHandler\PsrLogErrorHandler;
use Apitte\Core\Mapping\Validator\IEntityValidator;
use Apitte\Core\Mapping\Validator\SymfonyValidator;
use Apitte\Core\Schema\Schema;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationFoobarController;
use Tests\Fixtures\Psr\DummyLogger;
use Tests\Toolkit\NeonLoader;

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
		]);
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(JsonDispatcher::class, $container->getService('api.core.dispatcher'));
	Assert::type(Schema::class, $container->getService('api.core.schema'));
	Assert::type(Application::class, $container->getService('api.core.application'));
});

// Annotations
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				AnnotationFoobarController::class,
			],
		]);
	}, 2);

	/** @var Container $container */
	$container = new $class();

	/** @var Schema $schema */
	$schema = $container->getService('api.core.schema');
	Assert::count(4, $schema->getEndpoints());
	Assert::equal(['GET'], $schema->getEndpoints()[0]->getMethods());
	Assert::equal('/api/v1/foobar/baz1', $schema->getEndpoints()[0]->getMask());
	Assert::equal('#^/api/v1/foobar/baz1$#', $schema->getEndpoints()[0]->getPattern());
	Assert::equal([], $schema->getEndpoints()[0]->getParameters());
	Assert::equal(AnnotationFoobarController::class, $schema->getEndpoints()[0]->getHandler()->getClass());
	Assert::equal('baz1', $schema->getEndpoints()[0]->getHandler()->getMethod());
});

// PsrErrorHandler
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				DummyLogger::class,
			],
		]);
	}, 3);

	/** @var Container $container */
	$container = new $class();

	/** @var IErrorHandler $errorHandler */
	$errorHandler = $container->getService('api.core.errorHandler');
	Assert::true($errorHandler instanceof PsrLogErrorHandler);
});

// Mapping > SymfonyValidator
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig(NeonLoader::load(<<<NEON
			services:
				validator:
					factory: Apitte\Core\Mapping\Validator\SymfonyValidator(
						Doctrine\Common\Annotations\AnnotationReader()
					)
					setup:
						- setConstraintValidatorFactory(Symfony\Component\Validator\ConstraintValidatorFactory())
			api:
				plugins:
					Apitte\Core\DI\Plugin\CoreMappingPlugin:
						request:
							validator: @validator
		NEON
		));
	}, 4);

	/** @var Container $container */
	$container = new $class();

	/** @var IEntityValidator $validator */
	$validator = $container->getService('api.mapping.request.entity.mapping.validator');
	Assert::type(SymfonyValidator::class, $validator);
});
