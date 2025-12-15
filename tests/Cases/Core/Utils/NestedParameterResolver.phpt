<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Utils\NestedParameterResolver;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// NestedParameterResolver::parsePath - simple parameter
Toolkit::test(function (): void {
	Assert::equal(['name'], NestedParameterResolver::parsePath('name'));
	Assert::equal(['userId'], NestedParameterResolver::parsePath('userId'));
});

// NestedParameterResolver::parsePath - bracket notation
Toolkit::test(function (): void {
	Assert::equal(['page', 'number'], NestedParameterResolver::parsePath('page[number]'));
	Assert::equal(['page', 'size'], NestedParameterResolver::parsePath('page[size]'));
	Assert::equal(['filter', 'status'], NestedParameterResolver::parsePath('filter[status]'));
	Assert::equal(['filter', 'user', 'id'], NestedParameterResolver::parsePath('filter[user][id]'));
});

// NestedParameterResolver::parsePath - colon notation
Toolkit::test(function (): void {
	Assert::equal(['page', 'number'], NestedParameterResolver::parsePath('page:number'));
	Assert::equal(['page', 'size'], NestedParameterResolver::parsePath('page:size'));
	Assert::equal(['filter', 'status'], NestedParameterResolver::parsePath('filter:status'));
	Assert::equal(['filter', 'user', 'id'], NestedParameterResolver::parsePath('filter:user:id'));
});

// NestedParameterResolver::isNested
Toolkit::test(function (): void {
	Assert::false(NestedParameterResolver::isNested('name'));
	Assert::true(NestedParameterResolver::isNested('page[number]'));
	Assert::true(NestedParameterResolver::isNested('page:number'));
});

// NestedParameterResolver::getValue - simple parameter
Toolkit::test(function (): void {
	$data = ['name' => 'John', 'age' => 30];
	Assert::equal('John', NestedParameterResolver::getValue($data, 'name'));
	Assert::equal(30, NestedParameterResolver::getValue($data, 'age'));
	Assert::null(NestedParameterResolver::getValue($data, 'missing'));
	Assert::equal('default', NestedParameterResolver::getValue($data, 'missing', 'default'));
});

// NestedParameterResolver::getValue - bracket notation (JSON:API style)
Toolkit::test(function (): void {
	// This is how PHP parses ?page[number]=5&page[size]=10
	$data = [
		'page' => [
			'number' => 5,
			'size' => 10,
		],
		'filter' => [
			'status' => 'active',
			'user' => [
				'id' => 123,
			],
		],
	];

	Assert::equal(5, NestedParameterResolver::getValue($data, 'page[number]'));
	Assert::equal(10, NestedParameterResolver::getValue($data, 'page[size]'));
	Assert::equal('active', NestedParameterResolver::getValue($data, 'filter[status]'));
	Assert::equal(123, NestedParameterResolver::getValue($data, 'filter[user][id]'));
	Assert::null(NestedParameterResolver::getValue($data, 'page[missing]'));
	Assert::null(NestedParameterResolver::getValue($data, 'missing[key]'));
});

// NestedParameterResolver::getValue - colon notation
Toolkit::test(function (): void {
	$data = [
		'page' => [
			'number' => 5,
			'size' => 10,
		],
	];

	Assert::equal(5, NestedParameterResolver::getValue($data, 'page:number'));
	Assert::equal(10, NestedParameterResolver::getValue($data, 'page:size'));
});

// NestedParameterResolver::hasValue
Toolkit::test(function (): void {
	$data = [
		'name' => 'John',
		'page' => [
			'number' => 5,
		],
	];

	Assert::true(NestedParameterResolver::hasValue($data, 'name'));
	Assert::true(NestedParameterResolver::hasValue($data, 'page[number]'));
	Assert::true(NestedParameterResolver::hasValue($data, 'page:number'));
	Assert::false(NestedParameterResolver::hasValue($data, 'missing'));
	Assert::false(NestedParameterResolver::hasValue($data, 'page[size]'));
});

// NestedParameterResolver::setValue - simple parameter
Toolkit::test(function (): void {
	$data = ['name' => 'John'];
	$result = NestedParameterResolver::setValue($data, 'age', 30);
	Assert::equal(['name' => 'John', 'age' => 30], $result);
});

// NestedParameterResolver::setValue - bracket notation
Toolkit::test(function (): void {
	$data = [];
	$result = NestedParameterResolver::setValue($data, 'page[number]', 5);
	Assert::equal(['page' => ['number' => 5]], $result);

	$result = NestedParameterResolver::setValue($result, 'page[size]', 10);
	Assert::equal(['page' => ['number' => 5, 'size' => 10]], $result);
});

// NestedParameterResolver::setValue - colon notation
Toolkit::test(function (): void {
	$data = [];
	$result = NestedParameterResolver::setValue($data, 'page:number', 5);
	Assert::equal(['page' => ['number' => 5]], $result);
});
