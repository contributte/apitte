<?php declare(strict_types = 1);

use Apitte\Core\DI\ApiExtension;
use Apitte\Core\LinkGenerator\LinkGenerator;
use Apitte\Core\LinkGenerator\LinkGeneratorException;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationFoobarController;
use Tests\Fixtures\Controllers\UsersController;

require_once __DIR__ . '/../../../bootstrap.php';

// LinkGenerator is registered in DI
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
		]);
	}, 'linkGenerator1');

	/** @var Container $container */
	$container = new $class();

	Assert::type(LinkGenerator::class, $container->getService('api.core.linkGenerator'));
});

// LinkGenerator - link by Controller::method
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
	}, 'linkGenerator2');

	/** @var Container $container */
	$container = new $class();

	/** @var LinkGenerator $linkGenerator */
	$linkGenerator = $container->getService('api.core.linkGenerator');

	Assert::same('/api/v1/foobar/baz1', $linkGenerator->link(AnnotationFoobarController::class . '::baz1'));
	Assert::same('/api/v1/foobar/baz2', $linkGenerator->link(AnnotationFoobarController::class . '::baz2'));
});

// LinkGenerator - link by ID
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
	}, 'linkGenerator3');

	/** @var Container $container */
	$container = new $class();

	/** @var LinkGenerator $linkGenerator */
	$linkGenerator = $container->getService('api.core.linkGenerator');

	// ID hierarchy: testapi (AbstractController) . foobar (AnnotationFoobarController) . baz1 (method)
	Assert::same('/api/v1/foobar/baz1', $linkGenerator->link('testapi.foobar.baz1'));
});

// LinkGenerator - link with path parameters
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				UsersController::class,
			],
		]);
	}, 'linkGenerator4');

	/** @var Container $container */
	$container = new $class();

	/** @var LinkGenerator $linkGenerator */
	$linkGenerator = $container->getService('api.core.linkGenerator');

	// By controller::method
	Assert::same('/api/v1/users', $linkGenerator->link(UsersController::class . '::list'));
	Assert::same('/api/v1/users/123', $linkGenerator->link(UsersController::class . '::detail', ['id' => 123]));

	// By ID (testapi from AbstractController, users from UsersController)
	Assert::same('/api/v1/users', $linkGenerator->link('testapi.users.list'));
	Assert::same('/api/v1/users/456', $linkGenerator->link('testapi.users.detail', ['id' => 456]));
});

// LinkGenerator - link with query parameters
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				UsersController::class,
			],
		]);
	}, 'linkGenerator5');

	/** @var Container $container */
	$container = new $class();

	/** @var LinkGenerator $linkGenerator */
	$linkGenerator = $container->getService('api.core.linkGenerator');

	Assert::same('/api/v1/users?page=2&limit=10', $linkGenerator->link(UsersController::class . '::list', ['page' => 2, 'limit' => 10]));
	Assert::same('/api/v1/users/123?include=posts', $linkGenerator->link(UsersController::class . '::detail', ['id' => 123, 'include' => 'posts']));
});

// LinkGenerator - endpoint not found
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
		]);
	}, 'linkGenerator6');

	/** @var Container $container */
	$container = new $class();

	/** @var LinkGenerator $linkGenerator */
	$linkGenerator = $container->getService('api.core.linkGenerator');

	Assert::exception(
		fn () => $linkGenerator->link('nonexistent'),
		LinkGeneratorException::class,
		'Endpoint "nonexistent" not found'
	);
});

// LinkGenerator - missing required parameter
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				UsersController::class,
			],
		]);
	}, 'linkGenerator7');

	/** @var Container $container */
	$container = new $class();

	/** @var LinkGenerator $linkGenerator */
	$linkGenerator = $container->getService('api.core.linkGenerator');

	Assert::exception(
		fn () => $linkGenerator->link(UsersController::class . '::detail'),
		LinkGeneratorException::class,
		'Missing required parameter "id"'
	);
});
