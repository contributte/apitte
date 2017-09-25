<?php

/**
 * Test: Annotation\Controller\Group
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Group;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function () {
	$group = new Group(['value' => 'group']);
	Assert::equal('group', $group->getName());
	Assert::exception(
		function () {
			new Group(['value' => '']);
		},
		AnnotationException::class,
		'Empty @Group given'
	);
});

// Name
test(function () {
	$group = new Group(['name' => 'group']);
	Assert::equal('group', $group->getName());

	Assert::exception(
		function () {
			new Group(['name' => '']);
		},
		AnnotationException::class,
		'Empty @Group given'
	);
});

// Fails
test(function () {
	Assert::exception(
		function () {
			new Group(['name']);
		},
		AnnotationException::class,
		'No @Group given'
	);
	Assert::exception(
		function () {
			new Group(['value']);
		},
		AnnotationException::class,
		'No @Group given'
	);
	Assert::exception(
		function () {
			new Group(['a']);
		},
		AnnotationException::class,
		'No @Group given'
	);
	Assert::exception(
		function () {
			new Group([]);
		},
		AnnotationException::class,
		'No @Group given'
	);
});
