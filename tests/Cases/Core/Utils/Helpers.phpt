<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Utils\Helpers;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Helpers::slashless
Toolkit::test(function (): void {
	Assert::equal('/', Helpers::slashless('/'));
	Assert::equal('/', Helpers::slashless('//'));
	Assert::equal('/', Helpers::slashless('/////'));
	Assert::equal('/foo', Helpers::slashless('/foo'));
	Assert::equal('/foo', Helpers::slashless('//foo'));
	Assert::equal('/foo/', Helpers::slashless('/foo/'));
	Assert::equal('/foo/', Helpers::slashless('//foo//'));
});

// Helpers::callback
Toolkit::test(function (): void {
	Assert::type('callable', Helpers::callback([Helpers::class, 'callback']));

	Assert::exception(static function (): void {
		Assert::type('callable', Helpers::callback([Helpers::class, 'fake']));
	}, InvalidArgumentException::class);
});
