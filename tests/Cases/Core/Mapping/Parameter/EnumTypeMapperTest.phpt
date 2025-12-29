<?php declare(strict_types = 1);

namespace Tests\Cases\Core\Mapping\Parameter;

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\EnumTypeMapper;
use Apitte\Core\Schema\EndpointParameter;
use Tester\Assert;
use Tester\TestCase;

final class EnumTypeMapperTest extends TestCase
{

	public function testOk(): void
	{
		$mapper = new EnumTypeMapper();
		$endpointParameter = new EndpointParameter(
			name: 'foobar',
			type: EndpointParameter::TYPE_STRING,
		);
		$endpointParameter->setEnum(['', 'bar', 42]);
		$options = [
			'endpoint' => $endpointParameter,
		];

		Assert::same('', $mapper->normalize('', $options));
		Assert::same('bar', $mapper->normalize('bar', $options));
		Assert::same(42, $mapper->normalize(42, $options));
	}

	public function testFail(): void
	{
		$mapper = new EnumTypeMapper();
		$endpointParameter = new EndpointParameter(
			name: 'foobar',
			type: EndpointParameter::TYPE_STRING,
		);
		$options = [
			'endpoint' => $endpointParameter,
		];

		Assert::exception(function () use ($mapper, $options): void {
			$mapper->normalize('', $options);
		}, InvalidArgumentTypeException::class);

		$options['endpoint']->setEnum(['foo', 'bar']);

		Assert::exception(function () use ($mapper, $options): void {
			$mapper->normalize(null, $options);
		}, InvalidArgumentTypeException::class);

		Assert::exception(function () use ($mapper, $options): void {
			$mapper->normalize('string', $options);
		}, InvalidArgumentTypeException::class);
	}

}

(new EnumTypeMapperTest())->run();
