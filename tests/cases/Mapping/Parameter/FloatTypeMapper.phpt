<?php declare(strict_types = 1);

/**
 * Test: Mapping\Parameter\FloatTypeMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\FloatTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class TestFloatTypeMapper extends TestCase
{

	public function testOk(): void
	{
		$mapper = new FloatTypeMapper();

		Assert::same(0.0, $mapper->normalize(0));
		Assert::same(13.0, $mapper->normalize('13'));
		Assert::same(1.99, $mapper->normalize('1.99'));
		Assert::same(-10.0, $mapper->normalize('-10'));
	}

	public function testFail(): void
	{
		$mapper = new FloatTypeMapper();

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('');
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize(null);
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('string');
		}, InvalidArgumentTypeException::class);
	}

}

(new TestFloatTypeMapper())->run();
