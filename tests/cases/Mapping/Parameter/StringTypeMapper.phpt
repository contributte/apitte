<?php

/**
 * Test: Mapping\Parameter\StringTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Mapping\Parameter\StringTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class TestStringTypeMapper extends TestCase
{

	public function testNormalize()
	{
		$floatTypeMapper = new StringTypeMapper;

		Assert::same(NULL, $floatTypeMapper->normalize(NULL));
		Assert::same('0', $floatTypeMapper->normalize(0));
		Assert::same('0.33', $floatTypeMapper->normalize(0.33));
		Assert::same('1.99', $floatTypeMapper->normalize(1.99));
		Assert::same('-10', $floatTypeMapper->normalize(-10));
	}

}

(new TestStringTypeMapper)->run();
