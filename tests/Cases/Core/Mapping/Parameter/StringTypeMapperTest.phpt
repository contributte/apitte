<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Mapping\Parameter\StringTypeMapper;
use Apitte\Core\Schema\EndpointParameter;
use Tester\Assert;
use Tester\TestCase;

final class StringTypeMapperTest extends TestCase
{

	public function testOk(): void
	{
		$mapper = new StringTypeMapper();
		$parameter = new EndpointParameter('foo', EndpointParameter::TYPE_STRING);

		Assert::same('0', $mapper->normalize(0, $parameter));
		Assert::same('0.33', $mapper->normalize(0.33, $parameter));
		Assert::same('1.99', $mapper->normalize(1.99, $parameter));
		Assert::same('-10', $mapper->normalize(-10, $parameter));
	}

	public function testFail(): void
	{
		$mapper = new StringTypeMapper();

		Assert::exception(function () use ($mapper): void {
			$mapper->normalize(['']);
		}, InvalidArgumentTypeException::class);
	}

}

(new StringTypeMapperTest())->run();
