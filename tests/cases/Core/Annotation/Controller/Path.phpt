<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Path
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Path;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
test(function (): void {
	$path = new Path('FakePath');
	Assert::same('FakePath', $path->getPath());
});

// Exception - empty path
test(function (): void {
	Assert::exception(function (): void {
		new Path('');
	}, AnnotationException::class, 'Empty @Path given');
});
