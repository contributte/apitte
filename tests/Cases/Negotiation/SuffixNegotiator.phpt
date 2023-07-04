<?php declare(strict_types = 1);

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointNegotiation;
use Apitte\Negotiation\Http\ArrayEntity;
use Apitte\Negotiation\SuffixNegotiator;
use Apitte\Negotiation\Transformer\JsonTransformer;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// No transformer
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		$negotiation = new SuffixNegotiator([]);
		$negotiation->negotiate(
			new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()),
			new ApiResponse(Psr7ResponseFactory::fromGlobal())
		);
	}, InvalidStateException::class, 'Please add at least one transformer');
});

// Null response (no suitable transformer)
Toolkit::test(function (): void {
	$negotiation = new SuffixNegotiator(['.json' => new JsonTransformer()]);

	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('https://contributte.org'));
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	Assert::null($negotiation->negotiate($request, $response));
});

// JSON negotiation (according to .json suffix in URL)
Toolkit::test(function (): void {
	$negotiation = new SuffixNegotiator(['json' => new JsonTransformer()]);

	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);
	$endpoint->addNegotiation($en = new EndpointNegotiation('.json'));

	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()->withNewUri('https://contributte.org/foo.json'));
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$response = $response->withEntity(ArrayEntity::from(['foo' => 'bar']))
		->withEndpoint($endpoint);

	// 2# Negotiate response (PSR7 body contains encoded json data)
	$res = $negotiation->negotiate($request, $response);
	Assert::equal('{"foo":"bar"}', (string) $res->getBody());
});
