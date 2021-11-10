<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

use Apitte\OpenApi\Schema\MediaType;
use Apitte\OpenApi\Schema\RequestBody;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class RequestBodyTest extends TestCase
{

	public function testOptional(): void
	{
		$body = new RequestBody();

		$content = [];
		$content['text/*'] = $mediaType1 = new MediaType();
		$body->addMediaType('text/*', $mediaType1);
		$body->addMediaType('application/json', $mediaType1); // Intentionally added twice, tests overriding
		$content['application/json'] = $mediaType2 = new MediaType();
		$body->addMediaType('application/json', $mediaType2);

		$body->setDescription('description');
		$body->setRequired(true);

		Assert::same($content, $body->getContent());
		Assert::same('description', $body->getDescription());
		Assert::true($body->isRequired());

		$realData = $body->toArray();
		$expectedData = [
			'description' => 'description',
			'content' => ['text/*' => [], 'application/json' => []],
			'required' => true,
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, RequestBody::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$body = new RequestBody();

		Assert::same([], $body->getContent());
		Assert::null($body->getDescription());
		Assert::false($body->isRequired());

		$realData = $body->toArray();
		$expectedData = ['content' => []];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, RequestBody::fromArray($realData)->toArray());
	}

}

(new RequestBodyTest())->run();
