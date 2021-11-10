<?php declare(strict_types = 1);

/**
 * Test: Mapping\Parameter\DateTimeTypeMapper
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\DateTimeTypeMapper;
use Tester\Assert;
use Tester\TestCase;

final class TestDateTimeTypeMapper extends TestCase
{

	public function testOk(): void
	{
		$mapper = new DateTimeTypeMapper();

		$datetime = $mapper->normalize('2010-12-07T23:00:00Z');
		Assert::type(DateTimeImmutable::class, $datetime);

		$datetime = $mapper->normalize('2010-12-07T23:00:00+01:00');
		Assert::type(DateTimeImmutable::class, $datetime);

		$datetime = $mapper->normalize((new DateTime('now'))->format(DATE_ATOM));
		Assert::type(DateTimeImmutable::class, $datetime);
	}

	public function testFail(): void
	{
		$mapper = new DateTimeTypeMapper();

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
			$mapper->normalize(true);
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('7.12.2010 23:00:00');
		}, InvalidArgumentTypeException::class);

		// Unfortunately not supported by PHP
		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('2010-12-07T23:00:00.000Z');
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize('2010-12-07T23:00:00');
		}, InvalidArgumentTypeException::class);
	}

}

(new TestDateTimeTypeMapper())->run();
