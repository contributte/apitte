<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Method
 */

use Apitte\Core\Annotation\Controller\Method;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Value
test(function (): void {
	$method = new Method(['value' => 'GET']);
	Assert::equal(['GET'], $method->getMethods());

	$method = new Method(['value' => ['GET', 'POST']]);
	Assert::equal(['GET', 'POST'], $method->getMethods());

	Assert::exception(function (): void {
		new Method(['value' => 0]);
	}, AnnotationException::class, 'Invalid @Method given');
});

// Methods
test(function (): void {
	$method = new Method(['methods' => ['GET', 'POST']]);
	Assert::equal(['GET', 'POST'], $method->getMethods());
});

// Method
test(function (): void {
	$method = new Method(['method' => 'GET']);
	Assert::equal(['GET'], $method->getMethods());
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new Method([]);
	}, AnnotationException::class, 'No @Method given');
});
