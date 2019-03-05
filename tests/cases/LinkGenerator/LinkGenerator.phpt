<?php declare(strict_types = 1);

/**
 * Test: LinkGenerator\LinkGenerator
 */

use Apitte\Core\Exception\Logical\InvalidLinkException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestScopeStorage;
use Apitte\Core\LinkGenerator\ControllerMapper;
use Apitte\Core\LinkGenerator\StrictLinkGenerator;
use Apitte\Core\Mapping\Parameter\StringTypeMapper;
use Apitte\Core\Mapping\RequestParameterMapping;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Tester\Assert;
use Tests\Fixtures\Controllers\FoobarController;

require_once __DIR__ . '/../../bootstrap.php';

// Invalid link destination
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$controllerMapper = new ControllerMapper();
	$requestParameterMapping = new RequestParameterMapping();
	$generator = new StrictLinkGenerator($schema, $storage, $controllerMapper, $requestParameterMapping);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('abcd');
	}, InvalidLinkException::class, 'Invalid link destination "abcd".');
});

// Controller not found in schema
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$controllerMapper = new ControllerMapper();
	$requestParameterMapping = new RequestParameterMapping();
	$generator = new StrictLinkGenerator($schema, $storage, $controllerMapper, $requestParameterMapping);

	Assert::exception(function () use ($generator): void {
		$link = $generator->link('Tests:Fixtures:Controllers:Foobar:baz1');
	}, InvalidStateException::class, 'Controller "Tests\Fixtures\Controllers\FoobarController" is missing in schema.');
});

// Controller found in schema, but not with requested method
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$controllerMapper = new ControllerMapper();
	$requestParameterMapping = new RequestParameterMapping();
	$generator = new StrictLinkGenerator($schema, $storage, $controllerMapper, $requestParameterMapping);

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
	$controllerMapper = new ControllerMapper();
	$requestParameterMapping = new RequestParameterMapping();
	$generator = new StrictLinkGenerator($schema, $storage, $controllerMapper, $requestParameterMapping);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz1'));
	$schema->addEndpoint($endpoint);

	Assert::same('', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1'));
});

// Endpoint found - with mask
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$controllerMapper = new ControllerMapper();
	$requestParameterMapping = new RequestParameterMapping();
	$generator = new StrictLinkGenerator($schema, $storage, $controllerMapper, $requestParameterMapping);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz1'));
	$endpoint->setMask('/api/v1/foo/bar/baz');
	$schema->addEndpoint($endpoint);

	Assert::same('/api/v1/foo/bar/baz', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1'));
});

// Endpoint found - with mask with string parameters
test(function (): void {
	$schema = new Schema();
	$storage = new RequestScopeStorage();
	$controllerMapper = new ControllerMapper();
	$requestParameterMapping = new RequestParameterMapping();
	$requestParameterMapping->addMapper(EndpointParameter::TYPE_STRING, StringTypeMapper::class);
	$generator = new StrictLinkGenerator($schema, $storage, $controllerMapper, $requestParameterMapping);

	$endpoint = new Endpoint(new EndpointHandler(FoobarController::class, 'baz1'));
	$endpoint->setMask('/api/v1/foo/{bar}/{baz}');

	$parameter1 = new EndpointParameter('bar');
	$endpoint->addParameter($parameter1);

	$parameter2 = new EndpointParameter('baz');
	$endpoint->addParameter($parameter2);

	$schema->addEndpoint($endpoint);

	Assert::same('/api/v1/foo/lorem/ipsum', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1', ['bar' => 'lorem', 'baz' => 'ipsum']));
	Assert::same('/api/v1/foo/lorem/ipsum#loremipsum', $generator->link('Tests:Fixtures:Controllers:Foobar:baz1#loremipsum', ['bar' => 'lorem', 'baz' => 'ipsum']));
});
