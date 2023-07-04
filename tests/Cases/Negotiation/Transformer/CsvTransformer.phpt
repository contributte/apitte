<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Http\CsvEntity;
use Apitte\Negotiation\Transformer\CsvTransformer;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Encode
Toolkit::test(function (): void {
	$transformer = new CsvTransformer();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$response = $response->withEntity(
		(new CsvEntity(null))
			->withRows([
				['1', '2', '3'],
				['4', '5', '6'],
				['7', '8', '9'],
			])->withHeader(['A', 'B', 'C'])
	);

	$response = $transformer->transform($request, $response);
	$response->getBody()->rewind();

	Assert::equal('A,B,C
1,2,3
4,5,6
7,8,9', $response->getContents());
});
