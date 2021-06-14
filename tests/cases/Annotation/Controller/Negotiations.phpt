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
		$negotiation1 = new Negotiation('json', true),
		$negotiation2 = new Negotiation('xml', false),
		$negotiation3 = new Negotiation('csv', false),
	]);

	Assert::same([$negotiation1, $negotiation2, $negotiation3], $negotiations->getNegotiations());
});

// Exception - empty negotiations
test(function (): void {
	Assert::exception(function (): void {
		new Negotiations([]);
	}, AnnotationException::class, 'Empty @Negotiations given');
});
