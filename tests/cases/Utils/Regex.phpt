<?php

/**
 * Test: Utils\Regex
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Utils\Regex;
use Tester\Assert;

// Regex::match
test(function () {
	Assert::equal(NULL, Regex::match('foo', '#\d+#'));
	Assert::equal(['foo'], Regex::match('foo', '#\w+#'));
	Assert::equal(['foo', 'foo'], Regex::match('foo', '#(\w+)#'));
});

// Regex::replace
test(function () {
	Assert::equal('foo', Regex::replace('foobar', '#bar#', NULL));
});

// Regex::replaceCallback
test(function () {
	Assert::equal('barfoo', Regex::replaceCallback('foo', '#(foo)#', function ($matches) {
		return 'bar' . $matches[1];
	}));
});
