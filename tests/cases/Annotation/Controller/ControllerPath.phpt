<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\ControllerPath
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\ControllerPath;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function (): void {
	$path = new ControllerPath(['value' => 'FakeControllerPath']);
	Assert::equal('FakeControllerPath', $path->getPath());
	Assert::exception(function (): void {
		new ControllerPath(['value' => '']);
	}, AnnotationException::class, 'Empty @ControllerPath given');
});

// Path
test(function (): void {
	$path = new ControllerPath(['path' => 'FakeControllerPath']);
	Assert::equal('FakeControllerPath', $path->getPath());

	Assert::exception(function (): void {
		new ControllerPath(['path' => '']);
	}, AnnotationException::class, 'Empty @ControllerPath given');
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new ControllerPath(['path']);
	}, AnnotationException::class, 'No @ControllerPath given');
	Assert::exception(function (): void {
		new ControllerPath(['value']);
	}, AnnotationException::class, 'No @ControllerPath given');
	Assert::exception(function (): void {
		new ControllerPath(['a']);
	}, AnnotationException::class, 'No @ControllerPath given');
	Assert::exception(function (): void {
		new ControllerPath([]);
	}, AnnotationException::class, 'No @ControllerPath given');
});
