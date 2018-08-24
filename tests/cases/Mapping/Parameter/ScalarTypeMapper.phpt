<?php declare(strict_types = 1);

/**
 * Test: Mapping\Parameter\ScalarTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Mapping\Parameter\ScalarTypeMapper;
use Apitte\Core\Schema\EndpointParameter;
use Tester\Assert;
use Tester\TestCase;

final class TestScalarTypeMapper extends TestCase
{

	public function testOk(): void
	{
		$mapper = new ScalarTypeMapper();
		$parameter = new EndpointParameter('foo', EndpointParameter::TYPE_SCALAR);

		// Nulls
		Assert::same(null, $mapper->normalize(null, $parameter));
		Assert::same(null, $mapper->normalize('', $parameter));

		// Booleans
		Assert::same(true, $mapper->normalize(true, $parameter));
		Assert::same(true, $mapper->normalize('true', $parameter));
		Assert::same(true, $mapper->normalize('1', $parameter));
		Assert::same(false, $mapper->normalize(false, $parameter));
		Assert::same(false, $mapper->normalize('false', $parameter));
		Assert::same(false, $mapper->normalize('0', $parameter));

		// Floats
		Assert::same(0.0, $mapper->normalize(0.0, $parameter));
		Assert::same(13.0, $mapper->normalize('13.0', $parameter));
		Assert::same(-10.0, $mapper->normalize('-10.0', $parameter));

		// Integers
		Assert::same(0, $mapper->normalize(0, $parameter));
		Assert::same(13, $mapper->normalize('13', $parameter));
		Assert::same(-10, $mapper->normalize('-10', $parameter));

		// Strings
		Assert::same('string', $mapper->normalize('string', $parameter));
	}

}

(new TestScalarTypeMapper())->run();
