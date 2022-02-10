<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\ExternalDocumentation;
use Tester\Assert;
use Tester\TestCase;

class ExternalDocumentationTest extends TestCase
{

	public function testOptional(): void
	{
		$documentation = new ExternalDocumentation('https://example.com');
		$documentation->setDescription('Find more info here');

		Assert::same('https://example.com', $documentation->getUrl());
		Assert::same('Find more info here', $documentation->getDescription());

		$realData = $documentation->toArray();
		$expectedData = [
			'url' => 'https://example.com',
			'description' => 'Find more info here',
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, ExternalDocumentation::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$documentation = new ExternalDocumentation('https://example.com');

		Assert::same('https://example.com', $documentation->getUrl());
		Assert::null($documentation->getDescription());

		$realData = $documentation->toArray();
		$expectedData = ['url' => 'https://example.com'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, ExternalDocumentation::fromArray($realData)->toArray());
	}

}

(new ExternalDocumentationTest())->run();
