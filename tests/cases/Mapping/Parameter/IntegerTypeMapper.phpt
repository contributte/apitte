<?php declare(strict_types = 1);

/**
 * Test: Mapping\Parameter\IntegerTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Mapping\Parameter\IntegerTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class TestIntegerTypeMapper extends TestCase
{

	public function testNormalize(): void
	{
		$floatTypeMapper = new IntegerTypeMapper();

		Assert::same(null, $floatTypeMapper->normalize(null));
		Assert::same(0, $floatTypeMapper->normalize(0));
		Assert::same(0, $floatTypeMapper->normalize('0.33'));
		Assert::same(1, $floatTypeMapper->normalize('1.99'));
		Assert::same(-10, $floatTypeMapper->normalize('-10'));
	}

}

(new TestIntegerTypeMapper())->run();
