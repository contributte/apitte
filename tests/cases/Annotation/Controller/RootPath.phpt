<?php

/**
 * Test: Annotation\Controller\RootPath
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RootPath;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function () {
	$rootPath = new RootPath(['value' => 'RootPath']);
	Assert::equal('RootPath', $rootPath->getPath());
	Assert::exception(function () {
		new RootPath(['value' => '']);
	}, AnnotationException::class, 'Empty @RootPath given');
});

// Path
test(function () {
	$RootPath = new RootPath(['path' => 'RootPath']);
	Assert::equal('RootPath', $RootPath->getPath());

	Assert::exception(function () {
		new RootPath(['path' => '']);
	}, AnnotationException::class, 'Empty @RootPath given');
});

// Fails
test(function () {
	Assert::exception(function () {
		new RootPath(['path']);
	}, AnnotationException::class, 'No @RootPath given');
	Assert::exception(function () {
		new RootPath(['value']);
	}, AnnotationException::class, 'No @RootPath given');
	Assert::exception(function () {
		new RootPath(['a']);
	}, AnnotationException::class, 'No @RootPath given');
	Assert::exception(function () {
		new RootPath([]);
	}, AnnotationException::class, 'No @RootPath given');
});
