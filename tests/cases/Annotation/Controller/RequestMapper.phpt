<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\RequestMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestMapper;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Entity
test(function (): void {
	$requestMapper = new RequestMapper(['entity' => 'Entity']);
	Assert::equal('Entity', $requestMapper->getEntity());

	Assert::exception(function (): void {
		new RequestMapper(['entity' => '']);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new RequestMapper(['entity']);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
	Assert::exception(function (): void {
		new RequestMapper(['a']);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
	Assert::exception(function (): void {
		new RequestMapper([]);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
});
