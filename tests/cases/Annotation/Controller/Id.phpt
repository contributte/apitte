<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Id
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Id;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function (): void {
	$id = new Id(['value' => 'Id']);
	Assert::equal('Id', $id->getName());
	Assert::exception(function (): void {
		new Id(['value' => '']);
	}, AnnotationException::class, 'Empty @Id given');
});

// Name
test(function (): void {
	$id = new Id(['name' => 'Id']);
	Assert::equal('Id', $id->getName());

	Assert::exception(function (): void {
		new Id(['name' => '']);
	}, AnnotationException::class, 'Empty @Id given');
});

// Fails
test(function (): void {
	Assert::exception(function (): void {
		new Id(['name']);
	}, AnnotationException::class, 'No @Id given');
	Assert::exception(function (): void {
		new Id(['value']);
	}, AnnotationException::class, 'No @Id given');
	Assert::exception(function (): void {
		new Id(['a']);
	}, AnnotationException::class, 'No @Id given');
	Assert::exception(function (): void {
		new Id([]);
	}, AnnotationException::class, 'No @Id given');
});
