<?php

/**
 * Test: Mapping\Parameter\FloatTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Mapping\Parameter\FloatTypeMapper;
use Tester\Assert;

final class TestFloatTypeMapper extends Tester\TestCase
{
	public function testNormalize()
	{
		$floatTypeMapper=new FloatTypeMapper;

		Assert::same(null, $floatTypeMapper->normalize(null));
		Assert::same(0.0, $floatTypeMapper->normalize(0));
		Assert::same(0.33, $floatTypeMapper->normalize('0.33'));
		Assert::same(1.99, $floatTypeMapper->normalize('1.99'));
		Assert::same(-10.0, $floatTypeMapper->normalize('-10'));
	}
}

(new TestFloatTypeMapper)->run();
