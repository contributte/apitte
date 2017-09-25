<?php

/**
 * Test: Annotation\Controller\GroupPath
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\GroupPath;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function () {
	$groupPath = new GroupPath(['value' => 'GroupPath']);
	Assert::equal('GroupPath', $groupPath->getPath());
	Assert::exception(function () {
		new GroupPath(['value' => '']);
	}, AnnotationException::class, 'Empty @GroupPath given');
});

// Path
test(function () {
	$GroupPath = new GroupPath(['path' => 'GroupPath']);
	Assert::equal('GroupPath', $GroupPath->getPath());

	Assert::exception(function () {
		new GroupPath(['path' => '']);
	}, AnnotationException::class, 'Empty @GroupPath given');
});

// Fails
test(function () {
	Assert::exception(function () {
		new GroupPath(['path']);
	}, AnnotationException::class, 'No @GroupPath given');
	Assert::exception(function () {
		new GroupPath(['value']);
	}, AnnotationException::class, 'No @GroupPath given');
	Assert::exception(function () {
		new GroupPath(['a']);
	}, AnnotationException::class, 'No @GroupPath given');
	Assert::exception(function () {
		new GroupPath([]);
	}, AnnotationException::class, 'No @GroupPath given');
});
