<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\RequestParameter
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Schema\EndpointParameter;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
test(function (): void {
	$requestParameter = new RequestParameter(
		'Parameter',
		EndpointParameter::TYPE_STRING,
		true,
		false,
		false,
		'Desc',
		EndpointParameter::IN_QUERY
	);

	Assert::same('Parameter', $requestParameter->getName());
	Assert::same('Desc', $requestParameter->getDescription());
	Assert::same(EndpointParameter::TYPE_STRING, $requestParameter->getType());
	Assert::same(EndpointParameter::IN_QUERY, $requestParameter->getIn());
});

// Exception - no type
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter('Param', '');
	}, AnnotationException::class, 'Empty @RequestParameter type given');
});
