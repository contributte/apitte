<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Utils\Regex;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Regex::match
Toolkit::test(function (): void {
	Assert::equal(null, Regex::match('foo', '#\d+#'));
	Assert::equal(['foo'], Regex::match('foo', '#\w+#'));
	Assert::equal(['foo', 'foo'], Regex::match('foo', '#(\w+)#'));
});

// Regex::replace
Toolkit::test(function (): void {
	Assert::equal('foo', Regex::replace('foobar', '#bar#', ''));
});

// Regex::replaceCallback
Toolkit::test(function (): void {
	Assert::equal('barfoo', Regex::replaceCallback('foo', '#(foo)#', function ($matches) {
		return 'bar' . $matches[1];
	}));
});
