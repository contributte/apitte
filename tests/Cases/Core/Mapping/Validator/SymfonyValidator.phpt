<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Mapping\Validator\SymfonyValidator;
use Doctrine\Common\Annotations\AnnotationReader;
use Tester\Assert;
use Tests\Fixtures\Mapping\Validator\SimpleEntity;

// Happy case
test(function (): void {
	$reader = new AnnotationReader();
	$validator = new SymfonyValidator($reader);

	$entity = (new SimpleEntity())->factory(['id' => 1]);
	$validator->validate($entity);
});

// Invalid value
test(function (): void {
	$reader = new AnnotationReader();
	$validator = new SymfonyValidator($reader);

	$entity = new SimpleEntity();
	$entity->factory(['id' => 'foo']);

	Assert::exception(static function () use ($entity, $validator) {
		$validator->validate($entity);
	}, ValidationException::class);
});
