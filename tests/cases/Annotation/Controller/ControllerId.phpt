<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\ControllerId
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\ControllerId;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function (): void {
	$controller = new ControllerId(['value' => 'controller']);
	Assert::equal('controller', $controller->getName());
	Assert::exception(function (): void {
		new ControllerId(['value' => '']);
	}, AnnotationException::class, 'Empty @ControllerId given');
});

// Name
test(function (): void {
	$controller = new ControllerId(['name' => 'controller']);
	Assert::equal('controller', $controller->getName());

	Assert::exception(function (): void {
		new ControllerId(['name' => '']);
	}, AnnotationException::class, 'Empty @ControllerId given');
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new ControllerId(['name']);
	}, AnnotationException::class, 'No @ControllerId given');
	Assert::exception(function (): void {
		new ControllerId(['value']);
	}, AnnotationException::class, 'No @ControllerId given');
	Assert::exception(function (): void {
		new ControllerId(['a']);
	}, AnnotationException::class, 'No @ControllerId given');
	Assert::exception(function (): void {
		new ControllerId([]);
	}, AnnotationException::class, 'No @ControllerId given');
});
