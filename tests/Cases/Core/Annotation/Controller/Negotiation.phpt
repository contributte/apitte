<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Negotiation
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Negotiation;
use Tester\Assert;
use Tests\Fixtures\Negotiation\FooRenderer;

// Value
test(function (): void {
	$negotiation = new Negotiation('json', true, FooRenderer::class);

	Assert::same('json', $negotiation->getSuffix());
	Assert::same(true, $negotiation->isDefault());
	Assert::same(FooRenderer::class, $negotiation->getRenderer());
});
