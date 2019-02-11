<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Method
 */

use Apitte\Core\Annotation\Controller\Method;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Ok
test(function (): void {
	$method = new Method(['value' => 'GET']);
	Assert::equal(['GET'], $method->getMethods());

	$method = new Method(['value' => ['GET', 'POST']]);
	Assert::equal(['GET', 'POST'], $method->getMethods());
});

// Empty method
test(function (): void {
	Assert::exception(function (): void {
		new Method([]);
	}, AnnotationException::class, 'No @Method given');

	Assert::exception(function (): void {
		new Method(['value' => []]);
	}, AnnotationException::class, 'Empty @Method given');

	Assert::exception(function (): void {
		new Method(['value' => '']);
	}, AnnotationException::class, 'Empty @Method given');
});
