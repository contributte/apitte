<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\RequestMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestMapper;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;

// OK
test(function (): void {
	$requestMapper1 = new RequestMapper([
		'entity' => FooEntity::class,
	]);
	Assert::same(FooEntity::class, $requestMapper1->getEntity());
	Assert::same(true, $requestMapper1->isValidation());

	$requestMapper2 = new RequestMapper([
		'entity' => FooEntity::class,
		'validation' => false,
	]);
	Assert::same(FooEntity::class, $requestMapper2->getEntity());
	Assert::same(false, $requestMapper2->isValidation());
});

// Exception - empty entity
test(function (): void {
	Assert::exception(function (): void {
		new RequestMapper([]);
	}, AnnotationException::class, 'No @RequestMapper entity given');

	Assert::exception(function (): void {
		new RequestMapper([
			'entity' => '',
		]);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
});
