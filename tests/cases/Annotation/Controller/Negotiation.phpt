<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\Negotiation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Negotiation;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;
use Tests\Fixtures\Negotiation\FooRenderer;
use Tests\Fixtures\Negotiation\InvalidRenderer;

// Value
test(function (): void {
	$negotiation = new Negotiation([
		'suffix' => 'json',
		'default' => true,
		'renderer' => FooRenderer::class,
	]);

	Assert::same('json', $negotiation->getSuffix());
	Assert::same(true, $negotiation->isDefault());
	Assert::same(FooRenderer::class, $negotiation->getRenderer());
});

// Exception - suffix
test(function (): void {
	Assert::exception(function (): void {
		$negotiation = new Negotiation([]);
	}, AnnotationException::class, 'Suffix is required at @Negotiation');
});

// Exception - invalid renderer - class does not exists
test(function (): void {
	Assert::exception(function (): void {
		$negotiation = new Negotiation([
			'suffix' => 'json',
			'renderer' => 'foobar',
		]);
	}, AnnotationException::class, 'Renderer "foobar" at @Negotiation does not exists');
});

// Exception - invalid renderer - does not implement __invoke
test(function (): void {
	Assert::exception(
		function (): void {
			$negotiation = new Negotiation([
				'suffix' => 'json',
				'renderer' => InvalidRenderer::class,
			]);
		},
		AnnotationException::class,
		sprintf('Renderer "%s" does not implement __invoke(ApiRequest $request, ApiResponse $response, array $context): ApiResponse', InvalidRenderer::class)
	);
});
