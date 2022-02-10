<?php declare(strict_types = 1);

/**
 * Test: Debig/Negotiation/Transformer/DebugDataTransformer
 */

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Debug\Negotiation\Transformer\DebugDataTransformer;
use Apitte\Negotiation\Http\ArrayEntity;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;
use Tracy\Debugger;

require_once __DIR__ . '/../../../../bootstrap.php';

// Warnings
Toolkit::test(function (): void {
	$transformer = new DebugDataTransformer();

	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$response = $response->withEntity(ArrayEntity::from(['foo' => 'bar']));
	$response = $transformer->transform($request, $response);

	$response->getBody()->rewind();
	$expected = version_compare(Debugger::VERSION, '2.8.0', '<') ? '
Apitte\Negotiation\Http\ArrayEntity %a%
   data protected => array (1)
   |  foo => "bar" (3)
' : '
Apitte\Negotiation\Http\ArrayEntity %a%
   data: array (1)
   |  \'foo\' => \'bar\'
';
	Assert::match(trim($expected), $response->getBody()->getContents());
});
