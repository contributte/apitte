<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Mapping\Validator\SymfonyValidator;
use Doctrine\Common\Annotations\AnnotationReader;
use Tester\Assert;
use Tests\Fixtures\Mapping\Validator\SimpleEntity;

// Happy case
test(function (): void {
	$validator = new SymfonyValidator(new AnnotationReader());

	$entity = (new SimpleEntity())->factory(['id' => 1, 'typedId' => 1]);
	$validator->validate($entity);
});

// Invalid value
test(function (): void {
	$validator = new SymfonyValidator(new AnnotationReader());

	$entity = (new SimpleEntity())->factory(['id' => 'foo', 'typedId' => 1]);

	Assert::exception(static function () use ($entity, $validator) {
		$validator->validate($entity);
	}, ValidationException::class);

	$entity = (new SimpleEntity())->factory(['id' => 1, 'typedId' => 'foo']);

	Assert::exception(static function () use ($entity, $validator) {
		$validator->validate($entity);
	}, ValidationException::class);
});

// Without annotation reader
test(function (): void {
	$validator = new SymfonyValidator();

	$entity = (new SimpleEntity())->factory(['id' => null, 'typedId' => 'foo']);

	Assert::exception(static function () use ($entity, $validator) {
		$validator->validate($entity);
	}, ValidationException::class);

	$entity = (new SimpleEntity())->factory(['id' => null, 'typedId' => 1]);

	Assert::noError(static function () use ($entity, $validator) {
		$validator->validate($entity);
	});
});
