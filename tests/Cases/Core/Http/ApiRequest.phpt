<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Entity
Toolkit::test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$apiRequest = new ApiRequest($request);
	$apiRequest = $apiRequest->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, new stdClass());

	Assert::type(stdClass::class, $apiRequest->getEntity());
});

// Parameters
Toolkit::test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$apiRequest = new ApiRequest($request);

	Assert::false($apiRequest->hasParameter('name'));
	Assert::false($apiRequest->hasParameter('fake'));
	Assert::equal(null, $apiRequest->getParameter('name'));
	Assert::equal('default', $apiRequest->getParameter('name', 'default'));
	Assert::equal([], $apiRequest->getParameters());
});

// Parameters > withAttribute
Toolkit::test(function (): void {
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
