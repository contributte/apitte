<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Id;
use Contributte\Tester\Toolkit;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
Toolkit::test(function (): void {
	$id = new Id('id');
	Assert::same('id', $id->getName());
});

// Exception - no name
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new Id('');
	}, AnnotationException::class, 'Empty @Id given');
});
