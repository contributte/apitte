<?php declare(strict_types = 1);

/**
 * Test: Router\SimpleRouter
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Router\SimpleRouter;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;

// Match parameter {id}
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/users/(?P<id>[^/]+)#');

	$id = new EndpointParameter('id');
	$endpoint->addParameter($id);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('http://example.com/users/22/');
	$request2 = $request->withNewUri('http://example.com/not-matched/');
	$router = new SimpleRouter($schema);
	$matched = $router->match($request);
	$notMatched = $router->match($request2);

	Assert::null($notMatched);
	Assert::type($request, $matched);
	Assert::true(isset($matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['id']));
	Assert::equal('22', $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['id']);
});

// Match parameters {foo}/{bar}
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/users/(?P<foo>[^/]+)/(?P<bar>[^/]+)#');

	$foo = new EndpointParameter('foo');
	$endpoint->addParameter($foo);

	$bar = new EndpointParameter('bar');
	$endpoint->addParameter($bar);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('http://example.com/users/1/baz');
	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::type($request, $matched);
	Assert::true(isset($matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['foo']));
	Assert::equal('1', $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['foo']);
	Assert::true(isset($matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['bar']));
	Assert::equal('baz', $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['bar']);
});

// Not match
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withMethod('POST');
	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::null($matched);
});
