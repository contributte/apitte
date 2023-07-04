<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Path;
use Contributte\Tester\Toolkit;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
Toolkit::test(function (): void {
	$path = new Path('FakePath');
	Assert::same('FakePath', $path->getPath());
});

// Exception - empty path
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new Path('');
	}, AnnotationException::class, 'Empty @Path given');
});
