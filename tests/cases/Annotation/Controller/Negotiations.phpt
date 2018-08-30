<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Negotiations
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Negotiation;
use Apitte\Core\Annotation\Controller\Negotiations;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
test(function (): void {
	$negotiations = new Negotiations([
		'value' => [
			$negotiation1 = new Negotiation([
				'suffix' => 'json',
				'default' => true,
			]),
			$negotiation2 = new Negotiation([
				'suffix' => 'xml',
				'default' => false,
			]),
			$negotiation3 = new Negotiation([
				'suffix' => 'csv',
				'default' => false,
			]),
		],
	]);

	Assert::same([$negotiation1, $negotiation2, $negotiation3], $negotiations->getNegotiations());
});

// Exception - empty negotiations
test(function (): void {
	Assert::exception(function (): void {
		new Negotiations([
			'value' => [],
		]);
	}, AnnotationException::class, 'Empty @Negotiations given');

	Assert::exception(function (): void {
		new Negotiations([]);
	}, AnnotationException::class, 'No @Negotiation given in @Negotiations');
});
