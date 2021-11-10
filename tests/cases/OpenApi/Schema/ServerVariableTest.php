<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\ServerVariable;
use Tester\Assert;
use Tester\TestCase;

class ServerVariableTest extends TestCase
{

	public function testOptional(): void
	{
		$variable = new ServerVariable('default');
		$variable->setDescription('description');
		$variable->setEnum(['foo', 'bar', 'baz']);

		Assert::same('default', $variable->getDefault());
		Assert::same('description', $variable->getDescription());
		Assert::same(['foo', 'bar', 'baz'], $variable->getEnum());

		$realData = $variable->toArray();
		$expectedData = [
			'enum' => ['foo', 'bar', 'baz'],
			'default' => 'default',
			'description' => 'description',
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, ServerVariable::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$variable = new ServerVariable('default');

		Assert::same('default', $variable->getDefault());
		Assert::null($variable->getDescription());
		Assert::same([], $variable->getEnum());

		$realData = $variable->toArray();
		$expectedData = ['default' => 'default'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, ServerVariable::fromArray($realData)->toArray());
	}

}

(new ServerVariableTest())->run();
