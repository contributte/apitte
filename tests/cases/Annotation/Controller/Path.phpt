<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Path
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Path;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function (): void {
	$path = new Path(['value' => 'Path']);
	Assert::equal('Path', $path->getPath());
	Assert::exception(function (): void {
		new Path(['value' => '']);
	}, AnnotationException::class, 'Empty @Path given');
});

// Path
test(function (): void {
	$Path = new Path(['path' => 'Path']);
	Assert::equal('Path', $Path->getPath());

	Assert::exception(function (): void {
		new Path(['path' => '']);
	}, AnnotationException::class, 'Empty @Path given');
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new Path(['path']);
	}, AnnotationException::class, 'No @Path given');
	Assert::exception(function (): void {
		new Path(['value']);
	}, AnnotationException::class, 'No @Path given');
	Assert::exception(function (): void {
		new Path(['a']);
	}, AnnotationException::class, 'No @Path given');
	Assert::exception(function (): void {
		new Path([]);
	}, AnnotationException::class, 'No @Path given');
});
