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
	$id = new Id([
		'value' => 'id',
	]);
	Assert::same('id', $id->getName());
});

// Exception - no name
test(function (): void {
	Assert::exception(function (): void {
		new Id([
			'value' => '',
		]);
	}, AnnotationException::class, 'Empty @Id given');

	Assert::exception(function (): void {
		new Id([]);
	}, AnnotationException::class, 'No @Id given');
});
