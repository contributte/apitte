<?php declare(strict_types = 1);

/**
 * Test: Mapping\Parameter\IntegerTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\IntegerTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class TestIntegerTypeMapper extends TestCase
{

	public function testOk(): void
	{
		$mapper = new IntegerTypeMapper();

		Assert::same(0, $mapper->normalize(0));
		Assert::same(13, $mapper->normalize('13'));
		Assert::same(-10, $mapper->normalize('-10'));
	}

	public function testFail(): void
	{
		$mapper = new IntegerTypeMapper();

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('');
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize(null);
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('string');
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('1.99');
		}, InvalidArgumentTypeException::class);
	}

}

(new TestIntegerTypeMapper())->run();
