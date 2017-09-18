<?php

/**
 * Test: Middlewares\Transformer\JsonTransformer
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Middlewares\Transformer\JsonTransformer;
use Tester\Assert;
use function GuzzleHttp\Psr7\stream_for;

// Encode
test(function () {
	$transformer = new JsonTransformer();
	$response = new ApiResponse();
	$response = $response->writeJsonBody(['foo' => 'bar']);

	$response = $transformer->encode($response);
	$response->getBody()->rewind();

	Assert::equal('{"foo":"bar"}', $response->getContents());
});

// Decode
test(function () {
	$transformer = new JsonTransformer();
	$request = ApiRequest::fromGlobals()
		->withBody(stream_for('{"foo":"bar"}'));

	Assert::equal(['foo' => 'bar'], $transformer->decode($request)->getParsedBody());
});
