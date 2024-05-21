<?php declare(strict_types = 1);

use Apitte\Core\Application\Application;
use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Dispatcher\JsonDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\ErrorHandler\PsrLogErrorHandler;
use Apitte\Core\Mapping\Parameter\StringTypeMapper;
use Apitte\Core\Mapping\RequestParameterMapping;
use Apitte\Core\Mapping\Validator\IEntityValidator;
use Apitte\Core\Mapping\Validator\SymfonyValidator;
use Apitte\Core\Schema\Schema;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\Neonkit;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationFoobarController;
use Tests\Fixtures\Psr\DummyLogger;

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
		]);
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(JsonDispatcher::class, $container->getService('api.core.dispatcher'));
	Assert::type(Schema::class, $container->getService('api.core.schema'));
	Assert::type(Application::class, $container->getService('api.core.application'));
});

// Annotations
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
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
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
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
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig(Neonkit::load(<<<'NEON'
			services:
				validator:
					factory: Apitte\Core\Mapping\Validator\SymfonyValidator(
						Doctrine\Common\Annotations\AnnotationReader()
					)
					setup:
						- setConstraintValidatorFactory(Symfony\Component\Validator\ConstraintValidatorFactory())
						- setTranslator(Symfony\Component\Translation\Translator(en))
						- setTranslationDomain(validators)
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

// Mapping - custom type
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig(Neonkit::load(<<<'NEON'
			api:
				plugins:
					Apitte\Core\DI\Plugin\CoreMappingPlugin:
						types:
							bar: Apitte\Core\Mapping\Parameter\StringTypeMapper
		NEON
		));
	}, 5);

	/** @var Container $container */
	$container = new $class();

	/** @var RequestParameterMapping $mapping*/
	$mapping = $container->getService('api.mapping.request.parameters.mapping');
	Assert::type(StringTypeMapper::class, $mapping->getMapper('bar'));
});
