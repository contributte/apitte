<?php declare(strict_types = 1);

/**
 * Test: Transformer\JsonTransformer
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Http\ArrayEntity;
use Apitte\Negotiation\Transformer\JsonTransformer;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;

// Encode
test(function (): void {
	$transformer = new JsonTransformer();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$response = $response->withEntity(ArrayEntity::from(['foo' => 'bar']));

	$response = $transformer->transform($request, $response);
	$response->getBody()->rewind();

	Assert::equal('{"foo":"bar"}', $response->getContents());
});
