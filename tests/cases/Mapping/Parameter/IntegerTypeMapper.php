<?php

/**
 * Test: Mapping\Parameter\IntegerTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Mapping\Parameter\IntegerTypeMapper;
use Tester\Assert;

final class TestIntegerTypeMapper extends Tester\TestCase
{
	public function testNormalize()
	{
		$floatTypeMapper = new IntegerTypeMapper;

		Assert::same(NULL, $floatTypeMapper->normalize(NULL));
		Assert::same(0, $floatTypeMapper->normalize(0));
		Assert::same(0, $floatTypeMapper->normalize('0.33'));
		Assert::same(1, $floatTypeMapper->normalize('1.99'));
		Assert::same(-10, $floatTypeMapper->normalize('-10'));
	}
}

(new TestIntegerTypeMapper)->run();
