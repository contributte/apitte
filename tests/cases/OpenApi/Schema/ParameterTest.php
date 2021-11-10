<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\OpenApi\Schema\Parameter;
use Apitte\OpenApi\Schema\Reference;
use Apitte\OpenApi\Schema\Schema;
use Tester\Assert;
use Tester\TestCase;

class ParameterTest extends TestCase
{

	public function testOptional(): void
	{
		$parameter = new Parameter('p1', Parameter::IN_COOKIE);
		$parameter->setDescription('description');

		$parameter->setRequired(true);
		$parameter->setDeprecated(true);
		$parameter->setAllowEmptyValue(true);

		$parameter->setStyle('whatever');
		$parameter->setExample('whatever');

		$schema = new Schema([]);
		$parameter->setSchema($schema);

		Assert::same('p1', $parameter->getName());
		Assert::same(Parameter::IN_COOKIE, $parameter->getIn());
		Assert::same('description', $parameter->getDescription());

		Assert::true($parameter->isRequired());
		Assert::true($parameter->isDeprecated());
		Assert::true($parameter->isAllowEmptyValue());

		Assert::same('whatever', $parameter->getStyle());
		Assert::same('whatever', $parameter->getExample());
		Assert::same($schema, $parameter->getSchema());

		$realData = $parameter->toArray();
		$expectedData = [
			'name' => 'p1',
			'in' => 'cookie',
			'description' => 'description',
			'required' => true,
			'schema' => [],
			'example' => 'whatever',
			'style' => 'whatever',
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Parameter::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$parameter = new Parameter('p1', Parameter::IN_PATH);

		Assert::same('p1', $parameter->getName());
		Assert::same(Parameter::IN_PATH, $parameter->getIn());
		Assert::null($parameter->getDescription());

		Assert::false($parameter->isRequired());
		Assert::false($parameter->isDeprecated());
		Assert::false($parameter->isAllowEmptyValue());

		Assert::null($parameter->getStyle());
		Assert::null($parameter->getExample());
		Assert::null($parameter->getSchema());

		$realData = $parameter->toArray();
		$expectedData = ['name' => 'p1', 'in' => 'path'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Parameter::fromArray($realData)->toArray());
	}

	public function testInvalidIn(): void
	{
		Assert::exception(static function (): void {
			new Parameter('foo', 'invalid');
		}, InvalidArgumentException::class, 'Invalid value "invalid" for attribute "in" given. It must be one of "cookie, header, path, query".');
	}

	public function testSchemaReference(): void
	{
		$parameter = new Parameter('p1', Parameter::IN_PATH);
		$schema = new Reference('ref');
		$parameter->setSchema($schema);

		Assert::same($schema, $parameter->getSchema());

		$realData = $parameter->toArray();
		$expectedData = ['name' => 'p1', 'in' => 'path', 'schema' => ['$ref' => 'ref']];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Parameter::fromArray($realData)->toArray());
	}

}

(new ParameterTest())->run();
