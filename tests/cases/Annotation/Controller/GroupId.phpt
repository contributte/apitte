<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\GroupId
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\GroupId;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function (): void {
	$group = new GroupId(['value' => 'group']);
	Assert::equal('group', $group->getName());
	Assert::exception(function (): void {
		new GroupId(['value' => '']);
	}, AnnotationException::class, 'Empty @GroupId given');
});

// Name
test(function (): void {
	$group = new GroupId(['name' => 'group']);
	Assert::equal('group', $group->getName());

	Assert::exception(function (): void {
		new GroupId(['name' => '']);
	}, AnnotationException::class, 'Empty @GroupId given');
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new GroupId(['name']);
	}, AnnotationException::class, 'No @GroupId given');
	Assert::exception(function (): void {
		new GroupId(['value']);
	}, AnnotationException::class, 'No @GroupId given');
	Assert::exception(function (): void {
		new GroupId(['a']);
	}, AnnotationException::class, 'No @GroupId given');
	Assert::exception(function (): void {
		new GroupId([]);
	}, AnnotationException::class, 'No @GroupId given');
});
