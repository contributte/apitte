<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Negotiation;
use Apitte\Core\Annotation\Controller\Negotiations;
use Contributte\Tester\Toolkit;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
Toolkit::test(function (): void {
	$negotiations = new Negotiations([
		$negotiation1 = new Negotiation('json', true),
		$negotiation2 = new Negotiation('xml', false),
		$negotiation3 = new Negotiation('csv', false),
	]);

	Assert::same([$negotiation1, $negotiation2, $negotiation3], $negotiations->getNegotiations());
});

// Exception - empty negotiations
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new Negotiations([]);
	}, AnnotationException::class, 'Empty @Negotiations given');
});
