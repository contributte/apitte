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
	$requestParameter = new RequestParameter([
		'name' => 'Parameter',
		'description' => 'Desc',
		'type' => EndpointParameter::TYPE_STRING,
		'in' => EndpointParameter::IN_QUERY,
	]);

	Assert::same('Parameter', $requestParameter->getName());
	Assert::same('Desc', $requestParameter->getDescription());
	Assert::same(EndpointParameter::TYPE_STRING, $requestParameter->getType());
	Assert::same(EndpointParameter::IN_QUERY, $requestParameter->getIn());
});

// Exception - no name
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter([]);
	}, AnnotationException::class, 'No @RequestParameter name given');

	Assert::exception(function (): void {
		new RequestParameter([
			'name' => '',
		]);
	}, AnnotationException::class, 'Empty @RequestParameter name given');
});

// Exception - no type
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter([
			'name' => 'Param',
		]);
	}, AnnotationException::class, 'No @RequestParameter type given');

	Assert::exception(function (): void {
		new RequestParameter([
			'name' => 'Param',
			'type' => '',
		]);
	}, AnnotationException::class, 'Empty @RequestParameter type given');
});
