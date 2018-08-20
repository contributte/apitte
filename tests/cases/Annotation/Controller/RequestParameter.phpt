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
	]);
	Assert::same('Parameter', $requestParameter->getName());
	Assert::same('Desc', $requestParameter->getDescription());
	Assert::null($requestParameter->getType());
	Assert::same(EndpointParameter::IN_PATH, $requestParameter->getIn());

	$requestParameter = new RequestParameter([
		'name' => 'Parameter',
		'type' => EndpointParameter::TYPE_STRING,
		'in' => EndpointParameter::IN_QUERY,
	]);
	Assert::same('Parameter', $requestParameter->getName());
	Assert::same(EndpointParameter::TYPE_STRING, $requestParameter->getType());
	Assert::null($requestParameter->getDescription());
	Assert::same(EndpointParameter::IN_QUERY, $requestParameter->getIn());

	$requestParameter = new RequestParameter([
		'name' => 'Parameter',
		'description' => 'Desc',
		'type' => EndpointParameter::TYPE_OBJECT,
	]);
	Assert::equal('Parameter', $requestParameter->getName());
	Assert::equal('Desc', $requestParameter->getDescription());
	Assert::equal(EndpointParameter::TYPE_OBJECT, $requestParameter->getType());
});

// Exception - no name
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter([]);
	}, AnnotationException::class, 'Empty @RequestParameter name given');

	Assert::exception(function (): void {
		new RequestParameter([
			'name' => '',
		]);
	}, AnnotationException::class, 'Empty @RequestParameter name given');
});

// Exception - no type nor description
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter([
			'name' => 'Param',
		]);
	}, AnnotationException::class, 'Non-empty type or description is required at @RequestParameter');
});

// Exception - invalid type
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter([
			'name' => 'Param',
			'type' => 'foo',
		]);
	}, AnnotationException::class, sprintf('Invalid @RequestParameter type "%s". Choose one of %s::TYPE_*', 'foo', EndpointParameter::class));
});

// Exception - invalid in
test(function (): void {
	Assert::exception(function (): void {
		new RequestParameter([
			'name' => 'Param',
			'type' => EndpointParameter::TYPE_STRING,
			'in' => 'foo',
		]);
	}, AnnotationException::class, sprintf('Invalid @RequestParameter in "%s". Choose one of %s::IN_*', 'foo', EndpointParameter::class));
});
