<?php declare(strict_types = 1);

use Apitte\Core\Exception\Logical\InvalidControllerException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\LinkGenerator\ControllerMapper;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Invalid mapping
test(function (): void {
	$mapper = new ControllerMapper();
	Assert::exception(
		function () use ($mapper): void {
			$mapper->setMapping([
				'*' => ['*', '*'],
			]);
		},
		InvalidStateException::class,
		'Invalid mapping mask for module "*".'
	);
});

// Controller not found
test(function (): void {
	$mapper = new ControllerMapper();

	Assert::exception(function () use ($mapper): void {
		$mapper->getControllerClass('Foo:Bar:Api:V1:Users');
	}, InvalidControllerException::class, 'Cannot load controller "Foo:Bar:Api:V1:Users", class "Foo\Bar\Api\V1\UsersController" was not found.');
});

// Controller without IController
test(function (): void {
	$mapper = new ControllerMapper();

	Assert::exception(function () use ($mapper): void {
		$mapper->getControllerClass('Tests:Fixtures:Controllers:NoInterfaceInvalid');
	}, InvalidControllerException::class, 'Cannot load controller "Tests:Fixtures:Controllers:NoInterfaceInvalid", class "Tests\Fixtures\Controllers\NoInterfaceInvalidController" is not "Apitte\Core\UI\Controller\IController" implementor.');
});

// Abstract controller
test(function (): void {
	$mapper = new ControllerMapper();

	Assert::exception(function () use ($mapper): void {
		$mapper->getControllerClass('Tests:Fixtures:Controllers:Abstract');
	}, InvalidControllerException::class, 'Cannot load controller "Tests:Fixtures:Controllers:Abstract", class "Tests\Fixtures\Controllers\AbstractController" is abstract.');
});
