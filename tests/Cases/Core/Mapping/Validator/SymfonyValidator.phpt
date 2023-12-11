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

	$entity = (new SimpleEntity())->factory(['id' => 1, 'typedId' => 1]);
	$validator->validate($entity);
});

// Invalid value
Toolkit::test(function (): void {
	$validator = new SymfonyValidator(new AnnotationReader());

	$entity = (new SimpleEntity())->factory(['id' => 1, 'typedId' => 'foo']);

	$e = Assert::exception(static function () use ($entity, $validator): void {
		$validator->validate($entity);
	}, ValidationException::class);

	assert($e instanceof ValidationException);

	Assert::same([
		'validation' => [
			'typedId' => [
				'This value should be of type integer.',
			],
		],
	], $e->getContext());
});

// Without annotation reader
Toolkit::test(function (): void {
	$validator = new SymfonyValidator();

	$entity = (new SimpleEntity())->factory(['id' => null, 'typedId' => 'foo']);

	$e = Assert::exception(static function () use ($entity, $validator): void {
		$validator->validate($entity);
	}, ValidationException::class);

	assert($e instanceof ValidationException);

	Assert::same([
		'validation' => [
			'typedId' => [
				'This value should be of type integer.',
			],
		],
	], $e->getContext());

	$entity = (new SimpleEntity())->factory(['id' => null, 'typedId' => 1]);

	Assert::noError(static function () use ($entity, $validator): void {
		$validator->validate($entity);
	});
});
