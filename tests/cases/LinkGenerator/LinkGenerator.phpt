<?php declare(strict_types = 1);

/**
 * Test: LinkGenerator\LinkGenerator
 */

use Apitte\Core\Exception\Logical\InvalidLinkException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestScopeStorage;
use Apitte\Core\LinkGenerator\LinkGenerator;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\Schema;
use Tester\Assert;
use Tests\Fixtures\Controllers\FoobarController;

require_once __DIR__ . '/../../bootstrap.php';

// Controller not found
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('Foo:Bar:Api:V1:Users:index');
	}, InvalidLinkException::class, 'Cannot load controller "Foo:Bar:Api:V1:Users", class "Foo\Bar\Api\V1\UsersController" was not found.');
});

// Controller without IController
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('Tests:Fixtures:Controllers:NoInterfaceInvalid:baz1');
	}, InvalidLinkException::class, 'Cannot load controller "Tests:Fixtures:Controllers:NoInterfaceInvalid", class "Tests\Fixtures\Controllers\NoInterfaceInvalidController" is not "Apitte\Core\UI\Controller\IController" implementor.');
});

// Abstract controller
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('Tests:Fixtures:Controllers:Abstract:baz1');
	}, InvalidLinkException::class, 'Cannot load controller "Tests:Fixtures:Controllers:Abstract", class "Tests\Fixtures\Controllers\AbstractController" is abstract.');
});

// Invalid link destination
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);;

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('abcd');
	}, InvalidLinkException::class, 'Invalid link destination "abcd".');
});

// Controller not found in schema
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('Tests:Fixtures:Controllers:Foobar:baz1');
	}, InvalidStateException::class, 'Controller "Tests\Fixtures\Controllers\FoobarController" is missing in schema.');
});

// Controller found in schema, but not with requested method
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz2'));
	$schema->addEndpoint($endpoint);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('Tests:Fixtures:Controllers:Foobar:baz1');
	}, InvalidStateException::class, 'Controllers "Tests\Fixtures\Controllers\FoobarController" method "baz1" is missing in schema.');
});

// Endpoint found - empty mask
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz1'));
	$schema->addEndpoint($endpoint);

	Assert::same('', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1'));
});

// Endpoint found - with mask
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz1'));
	$endpoint->setMask('/api/v1/foo/bar/baz');
	$schema->addEndpoint($endpoint);

	Assert::same('/api/v1/foo/bar/baz', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1'));
});

// Endpoint found - with mask with parameters
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$generator = new LinkGenerator($schema, $storage);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz1'));
	$endpoint->setMask('/api/v1/foo/{bar}/{baz}');
	$schema->addEndpoint($endpoint);

	Assert::same('/api/v1/foo/lorem/ipsum', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1', ['bar' => 'lorem', 'baz' => 'ipsum']));
	Assert::same('/api/v1/foo/lorem/ipsum#barbaz', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1#barbaz', ['bar' => 'lorem', 'baz' => 'ipsum']));
});

// Invalid mapping
test(function (): void {
	Assert::exception(
		function (): void {
			$schema = new Schema();
			$storage = new RequestScopeStorage();
			$generator = new LinkGenerator($schema, $storage);
			$generator->setMapping([
				'*' => ['*', '*'],
			]);
		},
		InvalidStateException::class,
		'Invalid mapping mask for module "*".'
	);
});
