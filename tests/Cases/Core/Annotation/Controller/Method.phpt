<?php declare(strict_types = 1);

use Apitte\Core\Annotation\Controller\Method;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';

// Ok
Toolkit::test(function (): void {
	$method = new Method(['GET']);
	Assert::equal(['GET'], $method->getMethods());

	$method = new Method(['GET', 'POST']);
	Assert::equal(['GET', 'POST'], $method->getMethods());
});

// Empty method
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new Method([]);
	}, InvalidArgumentException::class, 'Empty #[Method] given');

	Assert::exception(function (): void {
		new Method('');
	}, InvalidArgumentException::class, 'Empty #[Method] given');
});
