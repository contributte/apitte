<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\ResponseMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\ResponseMapper;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;
use Tests\Fixtures\Mapping\Response\FooEntity;

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
