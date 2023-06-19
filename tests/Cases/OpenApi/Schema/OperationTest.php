<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\Callback;
use Apitte\OpenApi\Schema\ExternalDocumentation;
use Apitte\OpenApi\Schema\Operation;
use Apitte\OpenApi\Schema\Parameter;
use Apitte\OpenApi\Schema\Reference;
use Apitte\OpenApi\Schema\RequestBody;
use Apitte\OpenApi\Schema\Responses;
use Apitte\OpenApi\Schema\SecurityRequirement;
use Apitte\OpenApi\Schema\Server;
use Tester\Assert;
use Tester\TestCase;

class OperationTest extends TestCase
{

	public function testOptional(): void
	{
		$responses = new Responses();
		$operation = new Operation($responses);

		$operation->setSummary('summary');
		$operation->setDescription('description');
		$operation->setOperationId('id');

		$requestBody = new RequestBody();
		$operation->setRequestBody($requestBody);

		$externalDocs = new ExternalDocumentation('https://external-docs.example.com');
		$operation->setExternalDocs($externalDocs);

		$parameters = [];
		$parameters['path-p1'] = $p1 = new Parameter('p1', Parameter::IN_PATH);
		$operation->addParameter($p1);
		$parameters['cookie-p2'] = $p2 = new Parameter('p2', Parameter::IN_COOKIE);
		$operation->addParameter($p2);
		$parameters[] = $p3 = new Reference('r1');
		$operation->addParameter($p3);

		$callbacks = [];
		$callbacks[] = $callback1 = new Callback([]);
		$operation->addCallback($callback1);
		$callbacks[] = $callback2 = new Callback([]);
		$operation->addCallback($callback2);
		$callbacks[] = $callback3 = new Reference('ref');
		$operation->addCallback($callback3);

		$securityRequirements = [];
		$securityRequirements[] = $sr1 = new SecurityRequirement([]);
		$operation->addSecurityRequirement($sr1);

		$servers = [];
		$servers[] = $server1 = new Server('https://server-one.example.com');
		$operation->addServer($server1);
		$servers[] = $server2 = new Server('https://server-two.example.com');
		$operation->addServer($server2);

		$operation->setTags(['foo', 'bar', 'baz']);
		$operation->setDeprecated(true);

		Assert::same('summary', $operation->getSummary());
		Assert::same('description', $operation->getDescription());
		Assert::same('id', $operation->getOperationId());
		Assert::same($requestBody, $operation->getRequestBody());
		Assert::same($externalDocs, $operation->getExternalDocs());

		Assert::same($parameters, $operation->getParameters());
		Assert::same($callbacks, $operation->getCallbacks());
		Assert::same($securityRequirements, $operation->getSecurity());
		Assert::same($servers, $operation->getServers());
		Assert::same(['foo', 'bar', 'baz'], $operation->getTags());
		Assert::same($responses, $operation->getResponses());

		Assert::true($operation->isDeprecated());

		$realData = $operation->toArray();
		$expectedData = [
			'deprecated' => true,
			'tags' => ['foo', 'bar', 'baz'],
			'summary' => 'summary',
			'description' => 'description',
			'externalDocs' => ['url' => 'https://external-docs.example.com'],
			'operationId' => 'id',
			'parameters' => [
				['name' => 'p1', 'in' => 'path'],
				['name' => 'p2', 'in' => 'cookie'],
				['$ref' => 'r1'],
			],
			'requestBody' => ['content' => []],
			'security' => [[]],
			'responses' => [],
			'servers' => [
				['url' => 'https://server-one.example.com'],
				['url' => 'https://server-two.example.com'],
			],
			'callbacks' => [[], [], ['$ref' => 'ref']],
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Operation::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$responses = new Responses();
		$operation = new Operation($responses);

		Assert::null($operation->getSummary());
		Assert::null($operation->getDescription());
		Assert::null($operation->getOperationId());
		Assert::null($operation->getRequestBody());
		Assert::null($operation->getExternalDocs());

		Assert::same([], $operation->getParameters());
		Assert::same([], $operation->getCallbacks());
		Assert::same([], $operation->getSecurity());
		Assert::same([], $operation->getServers());
		Assert::same([], $operation->getTags());
		Assert::same($responses, $operation->getResponses());

		Assert::false($operation->isDeprecated());

		$realData = $operation->toArray();
		$expectedData = ['responses' => []];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Operation::fromArray($realData)->toArray());
	}

	public function testRequestBodyReference(): void
	{
		$responses = new Responses();
		$operation = new Operation($responses);

		$requestBody = new Reference('ref');
		$operation->setRequestBody($requestBody);

		Assert::same($requestBody, $operation->getRequestBody());

		$realData = $operation->toArray();
		$expectedData = ['requestBody' => ['$ref' => 'ref'], 'responses' => []];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Operation::fromArray($realData)->toArray());
	}

}

(new OperationTest())->run();
