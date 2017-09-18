<?php

/**
 * Test: Middlewares\Negotiation\SuffixNegotiator
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Middlewares\Negotiation\SuffixNegotiator;
use Apitte\Core\Middlewares\Transformer\JsonTransformer;
use Tester\Assert;

// No transformer
test(function () {
	Assert::exception(function () {
		$negotiation = new SuffixNegotiator([]);
		$negotiation->negotiateResponse(ApiRequest::fromGlobals(), ApiResponse::fromGlobals());
	}, InvalidStateException::class, 'Please add at least one transformer');
});

// Same response (no suitable transformer)
test(function () {
	$negotiation = new SuffixNegotiator(['.json' => new JsonTransformer()]);

	$request = ApiRequest::fromGlobals()->withNewUri('https://contributte.org');
	$response = ApiResponse::fromGlobals();

	// 1# Negotiate request (same object as given);
	Assert::same($request, $negotiation->negotiateRequest($request, $response));

	// 2# Negotiate response (same object as given)
	Assert::same($response, $negotiation->negotiateResponse($request, $response));
});

// JSON negotiation (according to .json suffix in URL)
test(function () {
	$negotiation = new SuffixNegotiator(['.json' => new JsonTransformer()]);

	$request = ApiRequest::fromGlobals()->withNewUri('https://contributte.org/foo.json');
	$response = ApiResponse::fromGlobals();
	$response = $response->writeJsonBody(['foo' => 'bar']);

	// 1# Negotiate request
	$request = $negotiation->negotiateRequest($request, $response);

	// 2# Negotiate response (PSR7 body contains encoded json data)
	$res = $negotiation->negotiateResponse($request, $response);
	Assert::equal('{"foo":"bar"}', (string) $res->getBody());
});

// Fallback negotiation (*)
test(function () {
	$negotiation = new SuffixNegotiator(['*' => new JsonTransformer()]);

	$request = ApiRequest::fromGlobals()->withNewUri('https://contributte.org/foo.bar');
	$response = ApiResponse::fromGlobals();
	$response = $response->writeJsonBody(['foo' => 'bar']);

	// 1# Negotiate request
	$request = $negotiation->negotiateRequest($request, $response);

	// 2# Negotiate response (PSR7 body contains encoded json data)
	$res = $negotiation->negotiateResponse($request, $response);
	Assert::equal('{"foo":"bar"}', (string) $res->getBody());
});
