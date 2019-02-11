<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\GroupPath
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\GroupPath;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
test(function (): void {
	$path = new GroupPath([
		'value' => 'FakeGroupPath',
	]);
	Assert::same('FakeGroupPath', $path->getPath());
});

// Exception - empty path
test(function (): void {
	Assert::exception(function (): void {
		new GroupPath([
			'value' => '',
		]);
	}, AnnotationException::class, 'Empty @GroupPath given');

	Assert::exception(function (): void {
		new GroupPath([]);
	}, AnnotationException::class, 'No @GroupPath given');
});
