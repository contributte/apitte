<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Schema\EndpointParameter;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// OK
Toolkit::test(function (): void {
	$requestParameter = new RequestParameter(
		'Parameter',
		EndpointParameter::TYPE_STRING,
		EndpointParameter::IN_QUERY,
		true,
		false,
		false,
		'Desc'
	);

	Assert::same('Parameter', $requestParameter->getName());
	Assert::same(EndpointParameter::TYPE_STRING, $requestParameter->getType());
	Assert::same(EndpointParameter::IN_QUERY, $requestParameter->getIn());
	Assert::true($requestParameter->isRequired());
	Assert::false($requestParameter->isAllowEmpty());
	Assert::false($requestParameter->isDeprecated());
	Assert::same('Desc', $requestParameter->getDescription());
});

// OK - short
Toolkit::test(function (): void {
	$requestParameter = new RequestParameter(
		'Parameter',
		EndpointParameter::TYPE_STRING
	);

	Assert::same('Parameter', $requestParameter->getName());
	Assert::same(EndpointParameter::TYPE_STRING, $requestParameter->getType());
	Assert::same(EndpointParameter::IN_PATH, $requestParameter->getIn());
	Assert::true($requestParameter->isRequired());
	Assert::false($requestParameter->isAllowEmpty());
	Assert::false($requestParameter->isDeprecated());
	Assert::null($requestParameter->getDescription());
});

// Exception - no type
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter('Param', '', EndpointParameter::IN_PATH);
	}, InvalidArgumentException::class, 'Empty #[RequestParameter] type given');
});

// Exception - invalid parameter location
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter('Param', EndpointParameter::TYPE_STRING, 'invalid');
	}, InvalidArgumentException::class, 'Invalid #[RequestParameter] in given');
});
