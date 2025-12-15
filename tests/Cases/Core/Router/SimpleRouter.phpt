<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Router\SimpleRouter;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Match parameter {id}
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/users/(?P<id>[^/]+)#');

	$id = new EndpointParameter('id');
	$endpoint->addParameter($id);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('http://example.com/users/22/');
	$request = new ApiRequest($request);
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
Toolkit::test(function (): void {
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
	$request = new ApiRequest($request);
	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::type($request, $matched);
	Assert::true(isset($matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['foo']));
	Assert::equal('1', $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['foo']);
	Assert::true(isset($matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['bar']));
	Assert::equal('baz', $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS)['bar']);
});

// Matched second endpoint, first have invalid method
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$schema = new Schema();

	$endpoint1 = new Endpoint($handler);
	$endpoint1->addMethod('GET');
	$endpoint1->setPattern('#/foo#');
	$schema->addEndpoint($endpoint1);

	$endpoint2 = new Endpoint($handler);
	$endpoint2->addMethod('POST');
	$endpoint2->setPattern('#/foo#');
	$schema->addEndpoint($endpoint2);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('http://example.com/foo')
		->withMethod('POST');
	$request = new ApiRequest($request);
	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::same($matched->getAttribute(RequestAttributes::ATTR_ENDPOINT), $endpoint2);
});

// Not matched, invalid method
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#/foo#');
	$endpoint->setMask('/foo');

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('http://example.com/foo')
		->withMethod('POST');
	$request = new ApiRequest($request);
	$router = new SimpleRouter($schema);

	Assert::exception(function () use ($router, $request): void {
		$router->match($request);
	}, ClientErrorException::class, 'Method "POST" is not allowed for endpoint "/foo".');
});

// Not matched, invalid url
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#/foo#');

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withMethod('GET');
	$request = new ApiRequest($request);
	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::null($matched);
});

// Match JSON:API style query parameters with bracket notation (page[number], page[size])
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/users#');

	// JSON:API style pagination parameters
	$pageNumber = new EndpointParameter('page[number]', EndpointParameter::TYPE_STRING);
	$pageNumber->setIn(EndpointParameter::IN_QUERY);
	$endpoint->addParameter($pageNumber);

	$pageSize = new EndpointParameter('page[size]', EndpointParameter::TYPE_STRING);
	$pageSize->setIn(EndpointParameter::IN_QUERY);
	$endpoint->addParameter($pageSize);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	// Simulate PHP parsing of ?page[number]=5&page[size]=10
	// PHP parses this into nested array: ['page' => ['number' => '5', 'size' => '10']]
	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withNewUri('http://example.com/users')
		->withQueryParams(['page' => ['number' => '5', 'size' => '10']]);
	$request = new ApiRequest($request);

	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::type($request, $matched);
	$params = $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS);
	Assert::equal('5', $params['page[number]']);
	Assert::equal('10', $params['page[size]']);
});

// Match JSON:API style filter parameters (filter[status], filter[user][id])
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/orders#');

	// JSON:API style filter parameters
	$filterStatus = new EndpointParameter('filter[status]', EndpointParameter::TYPE_STRING);
	$filterStatus->setIn(EndpointParameter::IN_QUERY);
	$endpoint->addParameter($filterStatus);

	$filterUserId = new EndpointParameter('filter[user][id]', EndpointParameter::TYPE_STRING);
	$filterUserId->setIn(EndpointParameter::IN_QUERY);
	$endpoint->addParameter($filterUserId);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	// Simulate PHP parsing of ?filter[status]=active&filter[user][id]=123
	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withNewUri('http://example.com/orders')
		->withQueryParams([
			'filter' => [
				'status' => 'active',
				'user' => ['id' => '123'],
			],
		]);
	$request = new ApiRequest($request);

	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::type($request, $matched);
	$params = $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS);
	Assert::equal('active', $params['filter[status]']);
	Assert::equal('123', $params['filter[user][id]']);
});

// Match colon notation query parameters (page:number)
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/items#');

	// Colon notation parameters
	$pageNumber = new EndpointParameter('page:number', EndpointParameter::TYPE_STRING);
	$pageNumber->setIn(EndpointParameter::IN_QUERY);
	$endpoint->addParameter($pageNumber);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	// For colon notation, the data is still nested (application-specific parsing)
	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withNewUri('http://example.com/items')
		->withQueryParams(['page' => ['number' => '3']]);
	$request = new ApiRequest($request);

	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::type($request, $matched);
	$params = $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS);
	Assert::equal('3', $params['page:number']);
});

// Missing optional JSON:API parameter returns null
Toolkit::test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addMethod('GET');
	$endpoint->setPattern('#^/users#');

	$pageNumber = new EndpointParameter('page[number]', EndpointParameter::TYPE_STRING);
	$pageNumber->setIn(EndpointParameter::IN_QUERY);
	$pageNumber->setRequired(false);
	$endpoint->addParameter($pageNumber);

	$schema = new Schema();
	$schema->addEndpoint($endpoint);

	// No query params provided
	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withNewUri('http://example.com/users')
		->withQueryParams([]);
	$request = new ApiRequest($request);

	$router = new SimpleRouter($schema);
	$matched = $router->match($request);

	Assert::type($request, $matched);
	$params = $matched->getAttribute(RequestAttributes::ATTR_PARAMETERS);
	Assert::null($params['page[number]']);
});
