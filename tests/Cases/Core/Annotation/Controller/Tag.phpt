<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Tag;
use Contributte\Tester\Toolkit;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// OK
Toolkit::test(function (): void {
	$tag = new Tag('name', null);

	Assert::same('name', $tag->getName());
	Assert::same(null, $tag->getValue());
});

// Exception - empty name
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new Tag('', null);
	}, AnnotationException::class, 'Empty @Tag name given');
});
