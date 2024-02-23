<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Mapping\Validator\SymfonyValidator;
use Contributte\Tester\Toolkit;
use Doctrine\Common\Annotations\AnnotationReader;
use Tester\Assert;
use Tests\Fixtures\Mapping\Validator\SimpleEntity;

// Happy case
Toolkit::test(function (): void {
	$validator = new SymfonyValidator(new AnnotationReader());

	$entity = (new SimpleEntity())->factory([
		'id' => 1,
		'typedId1' => 1,
		'typedId2' => 1,
		'typedId3' => 1,
		'typedId4' => 1,
	]);

	try {
		$validator->validate($entity);
		Assert::true(true);
	} catch (ValidationException $e) {
		Assert::fail('Validation should pass', null, null, $e);
	}
});

// Invalid value
Toolkit::test(function (): void {
	$validator = new SymfonyValidator(new AnnotationReader());

	$entity = (new SimpleEntity())->factory([
		'id' => 1,
		'typedId1' => 'foo',
		'typedId2' => 'foo',
		'typedId3' => 1,
		'typedId4' => 1,
	]);

	try {
		$validator->validate($entity);
		Assert::fail('Validation should fail');
	} catch (ValidationException $e) {
		Assert::equal([
			'validation' => [
				'typedId1' => ['This value should be of type integer.'],
				'typedId2' => ['This value should not be null.'],
			],
		], $e->getContext());
	}
});

// Without annotation reader
Toolkit::test(function (): void {
	$validator = new SymfonyValidator();

	$entity = (new SimpleEntity())->factory([
		'id' => null,
		'typedId1' => 1,
		'typedId2' => 'foo',
		'typedId3' => 1,
		'typedId4' => 1,
	]);

	try {
		$validator->validate($entity);
		Assert::fail('Validation should fail');
	} catch (ValidationException $e) {
		Assert::equal([
			'validation' => [
				'typedId2' => ['This value should not be null.'],
			],
		], $e->getContext());
	}

	$entity = (new SimpleEntity())->factory([
		'id' => null,
		'typedId1' => 1,
		'typedId2' => 1,
		'typedId3' => 1,
		'typedId4' => 1,
	]);

	Assert::noError(static function () use ($entity, $validator): void {
		$validator->validate($entity);
	});
});
