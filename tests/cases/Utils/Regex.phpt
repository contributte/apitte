<?php declare(strict_types = 1);

/**
 * Test: Utils\Regex
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Utils\Regex;
use Tester\Assert;

// Regex::match
test(function (): void {
	Assert::equal(null, Regex::match('foo', '#\d+#'));
	Assert::equal(['foo'], Regex::match('foo', '#\w+#'));
	Assert::equal(['foo', 'foo'], Regex::match('foo', '#(\w+)#'));
});

// Regex::replace
test(function (): void {
	Assert::equal('foo', Regex::replace('foobar', '#bar#', null));
});

// Regex::replaceCallback
test(function (): void {
	Assert::equal('barfoo', Regex::replaceCallback('foo', '#(foo)#', function ($matches) {
		return 'bar' . $matches[1];
	}));
});
