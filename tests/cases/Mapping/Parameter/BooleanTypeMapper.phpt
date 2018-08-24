<?php declare(strict_types = 1);

/**
 * Test: Mapping\Parameter\BooleanTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\BooleanTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class TestBooleanTypeMapper extends TestCase
{

	public function testOk(): void
	{
		$mapper = new BooleanTypeMapper();

		Assert::same(null, $mapper->normalize(null));
		Assert::same(null, $mapper->normalize(''));
		Assert::same(true, $mapper->normalize(true));
		Assert::same(true, $mapper->normalize('true'));
		Assert::same(true, $mapper->normalize('1'));
		Assert::same(false, $mapper->normalize(false));
		Assert::same(false, $mapper->normalize('false'));
		Assert::same(false, $mapper->normalize('0'));
	}

	public function testFail(): void
	{
		$mapper = new BooleanTypeMapper();

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('string');
		}, InvalidArgumentTypeException::class);
	}

}

(new TestBooleanTypeMapper())->run();
