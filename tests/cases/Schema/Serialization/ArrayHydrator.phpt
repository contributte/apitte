<?php declare(strict_types = 1);

/**
 * Test: Schema\Serialization\ArrayHydrator
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Tester\Assert;

// AddMethod: success
test(function (): void {
	$hydrator = new ArrayHydrator();

	Assert::same(true, true);
});
