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
		$negotiations = new Negotiations([
			'value' => [],
		]);
	}, AnnotationException::class, 'Empty @Negotiations given');

	Assert::exception(function (): void {
		$negotiations = new Negotiations([]);
	}, AnnotationException::class, 'No @Negotiation given in @Negotiations');
});

// Exception - multiple defaults
test(function (): void {
	Assert::exception(function (): void {
		$negotiations = new Negotiations([
			'value' => [
				$negotiation1 = new Negotiation([
					'suffix' => 'json',
					'default' => true,
				]),
				$negotiation2 = new Negotiation([
					'suffix' => 'xml',
					'default' => true,
				]),
			],
		]);
	}, AnnotationException::class, 'Multiple @Negotiation annotations with "default=true" given. Only one @Negotiation could be default.');
});

// Exception - collision of suffixes
test(function (): void {
	Assert::exception(function (): void {
		$negotiations = new Negotiations([
			'value' => [
				$negotiation1 = new Negotiation([
					'suffix' => 'json',
					'default' => true,
				]),
				$negotiation2 = new Negotiation([
					'suffix' => 'json',
					'default' => false,
				]),
			],
		]);
	}, AnnotationException::class, 'Multiple @Negotiation with "suffix=json" given. Each @Negotiation must have unique suffix');
});
