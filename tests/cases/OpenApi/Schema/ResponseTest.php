<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

use Apitte\OpenApi\Schema\Header;
use Apitte\OpenApi\Schema\Reference;
use Apitte\OpenApi\Schema\Response;
use Apitte\OpenApi\Schema\Schema;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class ResponseTest extends TestCase
{

	public function testOptional(): void
	{
		$array = [
			'description' => 'Description',
			'headers' => [
				'WWW-Authenticate' => [
					'description' => 'The authentication method that should be used to gain access to a resource',
					'schema' => ['type' => 'string'],
				],
			],
		];
		$response = new Response('Description');
		$header = new Header();
		$header->setDescription('The authentication method that should be used to gain access to a resource');
		$headerSchema = new Schema(['type' => 'string']);
		$header->setSchema($headerSchema);
		$response->setHeader('WWW-Authenticate', $header);
		Assert::same($array, $response->toArray());
		Assert::equal($response, Response::fromArray($array));
	}

	public function testRequired(): void
	{
		$array = ['description' => 'Description'];
		$response = new Response('Description');
		Assert::same($array, $response->toArray());
		Assert::equal($response, Response::fromArray($array));
	}

	public function testHeaderReference(): void
	{
		$array = [
			'description' => 'API key or user token is missing or invalid',
			'headers' => [
				'WWW-Authenticate' => [
					'$ref' => '#/components/header/WWW-Authenticate',
				],
			],
		];
		$response = new Response('API key or user token is missing or invalid');
		$headerReference = new Reference('#/components/header/WWW-Authenticate');
		$response->setHeader('WWW-Authenticate', $headerReference);
		Assert::same($array, $response->toArray());
		Assert::equal($response, Response::fromArray($array));
	}

}

(new ResponseTest())->run();
