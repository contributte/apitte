<?php

/**
 * Test: Annotation\Controller\ControllerPath
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\ControllerPath;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function () {
	$path = new ControllerPath(['value' => 'FakeControllerPath']);
	Assert::equal('FakeControllerPath', $path->getPath());
	Assert::exception(function () {
		new ControllerPath(['value' => '']);
	}, AnnotationException::class, 'Empty @ControllerPath given');
});

// Path
test(function () {
	$path = new ControllerPath(['path' => 'FakeControllerPath']);
	Assert::equal('FakeControllerPath', $path->getPath());

	Assert::exception(function () {
		new ControllerPath(['path' => '']);
	}, AnnotationException::class, 'Empty @ControllerPath given');
});

// Fails
test(function () {
	Assert::exception(function () {
		new ControllerPath(['path']);
	}, AnnotationException::class, 'No @ControllerPath given');
	Assert::exception(function () {
		new ControllerPath(['value']);
	}, AnnotationException::class, 'No @ControllerPath given');
	Assert::exception(function () {
		new ControllerPath(['a']);
	}, AnnotationException::class, 'No @ControllerPath given');
	Assert::exception(function () {
		new ControllerPath([]);
	}, AnnotationException::class, 'No @ControllerPath given');
});
