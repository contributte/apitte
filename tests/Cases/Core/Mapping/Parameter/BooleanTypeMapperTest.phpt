<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\BooleanTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class BooleanTypeMapperTest extends TestCase
{

	public function testOk(): void
	{
		$mapper = new BooleanTypeMapper();

		Assert::same(true, $mapper->normalize(true));
		Assert::same(true, $mapper->normalize('true'));
		Assert::same(false, $mapper->normalize(false));
		Assert::same(false, $mapper->normalize('false'));
	}

	public function testFail(): void
	{
		$mapper = new BooleanTypeMapper();

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
			$mapper->normalize('0');
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('1');
		}, InvalidArgumentTypeException::class);
	}

}

(new BooleanTypeMapperTest())->run();
