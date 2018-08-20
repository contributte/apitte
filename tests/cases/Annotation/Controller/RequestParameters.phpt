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
		'value' => [
			$parameter1 = new RequestParameter([
				'name' => 'foo',
				'type' => EndpointParameter::TYPE_STRING,
			]),
			$parameter2 = new RequestParameter([
				'name' => 'bar',
				'type' => EndpointParameter::TYPE_STRING,
			]),
			$parameter3 = new RequestParameter([
				'name' => 'baz',
				'type' => EndpointParameter::TYPE_STRING,
			]),
		],
	]);

	Assert::same([$parameter1, $parameter2, $parameter3], $parameters->getParameters());
});

// Exception - empty negotiations
test(function (): void {
	Assert::exception(function (): void {
		$parameters = new RequestParameters([]);
	}, AnnotationException::class, 'No @RequestParameter given in @RequestParameters');

	Assert::exception(function (): void {
		$parameters = new RequestParameters([
			'value' => [],
		]);
	}, AnnotationException::class, 'Empty @RequestParameters given');
});

// Exception - multiple parameters with same name and location
test(function (): void {
	Assert::exception(
		function (): void {
			$parameters = new RequestParameters([
				'value' => [
					$parameter1 = new RequestParameter([
						'name' => 'foo',
						'type' => EndpointParameter::TYPE_STRING,
						'in' => EndpointParameter::IN_QUERY,
					]),
					$parameter2 = new RequestParameter([
						'name' => 'foo',
						'type' => EndpointParameter::TYPE_INTEGER,
						'in' => EndpointParameter::IN_QUERY,
					]),
				],
			]);
		},
		AnnotationException::class,
		'Multiple @RequestParameter annotations with "name=foo" and "in=query" given. Each parameter must have unique combination of location and name.'
	);
});
