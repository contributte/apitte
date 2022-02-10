<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\ExternalDocumentation;
use Apitte\OpenApi\Schema\Tag;
use Tester\Assert;
use Tester\TestCase;

class TagTest extends TestCase
{

	public function testOptional(): void
	{
		$tag = new Tag('pet');
		$tag->setDescription('Pets operations');

		$externalDocs = new ExternalDocumentation('https://example.com');
		$tag->setExternalDocs($externalDocs);

		Assert::same('pet', $tag->getName());
		Assert::same('Pets operations', $tag->getDescription());
		Assert::same($externalDocs, $tag->getExternalDocs());

		$realData = $tag->toArray();
		$expectedData = [
			'name' => 'pet',
			'description' => 'Pets operations',
			'externalDocs' => ['url' => 'https://example.com'],
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Tag::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$tag = new Tag('pet');

		Assert::same('pet', $tag->getName());
		Assert::null($tag->getDescription());
		Assert::null($tag->getExternalDocs());

		$realData = $tag->toArray();
		$expectedData = ['name' => 'pet'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Tag::fromArray($realData)->toArray());
	}

}

(new TagTest())->run();
