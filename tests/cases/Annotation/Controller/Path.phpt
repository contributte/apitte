<?php

/**
 * Test: Annotation\Controller\Path
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Path;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function () {
	$path = new Path(['value' => 'Path']);
	Assert::equal('Path', $path->getPath());
	Assert::exception(function () {
		new Path(['value' => '']);
	}, AnnotationException::class, 'Empty @Path given');
});

// Path
test(function () {
	$Path = new Path(['path' => 'Path']);
	Assert::equal('Path', $Path->getPath());

	Assert::exception(function () {
		new Path(['path' => '']);
	}, AnnotationException::class, 'Empty @Path given');
});

// Fails
test(function () {
	Assert::exception(function () {
		new Path(['path']);
	}, AnnotationException::class, 'No @Path given');
	Assert::exception(function () {
		new Path(['value']);
	}, AnnotationException::class, 'No @Path given');
	Assert::exception(function () {
		new Path(['a']);
	}, AnnotationException::class, 'No @Path given');
	Assert::exception(function () {
		new Path([]);
	}, AnnotationException::class, 'No @Path given');
});
