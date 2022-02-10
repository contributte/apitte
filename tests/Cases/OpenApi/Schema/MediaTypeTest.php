<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

use Apitte\OpenApi\Schema\MediaType;
use Apitte\OpenApi\Schema\Reference;
use Apitte\OpenApi\Schema\Schema;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class MediaTypeTest extends TestCase
{

	public function testOptional(): void
	{
		$mediaType = new MediaType();
		$mediaType->setExample('whatever');

		$schema = new Schema([]);
		$mediaType->setSchema($schema);

		$realData = $mediaType->toArray();
		$expectedData = ['schema' => [], 'example' => 'whatever'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, MediaType::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$mediaType = new MediaType();

		Assert::null($mediaType->getExample());
		Assert::null($mediaType->getSchema());

		$realData = $mediaType->toArray();
		$expectedData = [];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, MediaType::fromArray($realData)->toArray());
	}

	public function testSchemaReference(): void
	{
		$mediaType = new MediaType();
		$schema = new Reference('ref');
		$mediaType->setSchema($schema);

		Assert::same($schema, $mediaType->getSchema());

		$realData = $mediaType->toArray();
		$expectedData = ['schema' => ['$ref' => 'ref']];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, MediaType::fromArray($realData)->toArray());
	}

}

(new MediaTypeTest())->run();
