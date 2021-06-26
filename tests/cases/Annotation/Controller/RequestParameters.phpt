<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\RequestParameters
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Schema\EndpointParameter;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
test(function (): void {
	$parameters = new RequestParameters([
		$parameter1 = new RequestParameter('foo', EndpointParameter::TYPE_STRING, EndpointParameter::IN_PATH),
		$parameter2 = new RequestParameter('bar', EndpointParameter::TYPE_STRING, EndpointParameter::IN_PATH),
		$parameter3 = new RequestParameter('baz', EndpointParameter::TYPE_STRING, EndpointParameter::IN_PATH),
	]);

	Assert::same([$parameter1, $parameter2, $parameter3], $parameters->getParameters());
});

// Exception - empty negotiations
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameters([]);
	}, AnnotationException::class, 'Empty @RequestParameters given');
});

// Exception - multiple parameters with same name and location
test(function (): void {
	Assert::exception(
		function (): void {
			new RequestParameters([
				$parameter1 = new RequestParameter(
					'foo',
					EndpointParameter::TYPE_STRING,
					EndpointParameter::IN_QUERY,
					false
				),
				$parameter2 = new RequestParameter(
					'foo',
					EndpointParameter::TYPE_INTEGER,
					EndpointParameter::IN_QUERY,
					false
				)]);
		},
		AnnotationException::class,
		'Multiple @RequestParameter annotations with "name=foo" and "in=query" given. Each parameter must have unique combination of location and name.'
	);
});
