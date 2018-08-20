<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\ResponseMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\ResponseMapper;
use Apitte\Core\Mapping\Response\IResponseEntity;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;
use Tests\Fixtures\Mapping\Response\FooEntity;
use Tests\Fixtures\Mapping\Response\InvalidEntity;

// OK
test(function (): void {
	$responseMapper1 = new ResponseMapper([
		'entity' => FooEntity::class,
	]);
	Assert::same(FooEntity::class, $responseMapper1->getEntity());
});

// Exception - empty entity
test(function (): void {
	Assert::exception(function (): void {
		new ResponseMapper([]);
	}, AnnotationException::class, 'Empty @ResponseMapper entity given');

	Assert::exception(function (): void {
		new ResponseMapper([
			'entity' => '',
		]);
	}, AnnotationException::class, 'Empty @ResponseMapper entity given');
});

// Exception - invalid entity
test(function (): void {
	Assert::exception(function (): void {
		new ResponseMapper([
			'entity' => 'foobar',
		]);
	}, AnnotationException::class, '@ResponseMapper entity "foobar" does not exists');

	Assert::exception(function (): void {
		new ResponseMapper([
			'entity' => InvalidEntity::class,
		]);
	}, AnnotationException::class, sprintf('@ResponseMapper entity "%s" does not implements "%s"', InvalidEntity::class, IResponseEntity::class));
});
