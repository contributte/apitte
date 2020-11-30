<?php declare(strict_types = 1);

/**
 * Test: Http\ApiRequest
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;

// Parameters
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$apiRequest = new ApiRequest($request);

	Assert::false($apiRequest->hasParameter('name'));
	Assert::false($apiRequest->hasParameter('fake'));
	Assert::equal(null, $apiRequest->getParameter('name'));
	Assert::equal('default', $apiRequest->getParameter('name', 'default'));
	Assert::equal([], $apiRequest->getParameters());
});

// Parameters > withAttribute
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$request = $request->withAttribute(RequestAttributes::ATTR_PARAMETERS, ['name' => 'John Doe', 'title' => null]);
	$apiRequest = new ApiRequest($request);

	Assert::true($apiRequest->hasParameter('name'));
	Assert::true($apiRequest->hasParameter('title'));
	Assert::false($apiRequest->hasParameter('fake'));
	Assert::equal('John Doe', $apiRequest->getParameter('name'));
	Assert::equal(['name' => 'John Doe', 'title' => null], $apiRequest->getParameters());
	Assert::false($apiRequest->hasParameter('company'));
	Assert::equal(null, $apiRequest->getParameter('company'));
	Assert::equal('default', $apiRequest->getParameter('company', 'default'));
});
