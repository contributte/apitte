<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\Server;
use Apitte\OpenApi\Schema\ServerVariable;
use Tester\Assert;
use Tester\TestCase;

class ServerTest extends TestCase
{

	public function testOptional(): void
	{
		$server = new Server('https://example.com');
		$server->setDescription('description');

		$variables = [];
		$variables['var1'] = $variable1 = new ServerVariable('default');
		$server->addVariable('var1', $variable1);
		$server->addVariable('var2', $variable1); // Intentionally added twice, tests overriding
		$variables['var2'] = $variable2 = new ServerVariable('default');
		$server->addVariable('var2', $variable2);

		Assert::same('https://example.com', $server->getUrl());
		Assert::same('description', $server->getDescription());
		Assert::same($variables, $server->getVariables());

		$realData = $server->toArray();
		$expectedData = [
			'url' => 'https://example.com',
			'description' => 'description',
			'variables' => [
				'var1' => ['default' => 'default'],
				'var2' => ['default' => 'default'],
			],
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Server::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$server = new Server('https://example.com');

		Assert::same('https://example.com', $server->getUrl());
		Assert::null($server->getDescription());
		Assert::same([], $server->getVariables());

		$realData = $server->toArray();
		$expectedData = ['url' => 'https://example.com'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Server::fromArray($realData)->toArray());
	}

}

(new ServerTest())->run();
