<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\ControllerPath
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\ControllerPath;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
test(function (): void {
	$path = new ControllerPath([
		'value' => 'FakeControllerPath',
	]);
	Assert::same('FakeControllerPath', $path->getPath());
});

// Exception - empty path
test(function (): void {
	Assert::exception(function (): void {
		new ControllerPath([
			'value' => '',
		]);
	}, AnnotationException::class, 'Empty @ControllerPath given');

	Assert::exception(function (): void {
		new ControllerPath([]);
	}, AnnotationException::class, 'No @ControllerPath given');
});
