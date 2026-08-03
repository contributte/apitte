<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\SchemaDefinition;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\EndpointResponse;
use Apitte\Core\Schema\Schema;
use Apitte\OpenApi\SchemaDefinition\CoreDefinition;
use Apitte\OpenApi\SchemaDefinition\Entity\EntityAdapter;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\RequestBody\SimpleRequestBody;
use Tests\Fixtures\ResponseEntity\EmptyResponseEntity;

final class CoreDefinitionTest extends TestCase
{

	public function testString(): void
	{
		$schema = new Schema();

		$endpoint = new Endpoint(new EndpointHandler('class', 'method'));
		$endpoint->setMask('/foo/bar');
		$endpoint->setMethods(['GET']);
		$endpoint->addTag('tag1');

		$requestBody = new EndpointRequestBody();
		$endpoint->setRequestBody($requestBody);

		$schema->addEndpoint($endpoint);

		$endpoint = new Endpoint(new EndpointHandler('class', 'method'));

		$endpoint->setMask('/foo/bar');
		$endpoint->setMethods(['POST', 'PUT']);
		$endpoint->addTag('tag2');
		$endpoint->addTag('tag3', 'value3');

		$requestBody = new EndpointRequestBody();
		$requestBody->setDescription('Description');
		$requestBody->setRequired(true);
		$requestBody->setEntity(SimpleRequestBody::class);
		$endpoint->setRequestBody($requestBody);

		$endpoint->setOpenApi([
			'controller' => [
				'info' => [
					'title' => 'Title',
					'version' => '1.0.0',
				],
			],
			'method' => [
				'description' => 'OpenApi description',
			],
		]);

		$response = new EndpointResponse('200', 'description');
		$response->setEntity(EmptyResponseEntity::class);
		$endpoint->addResponse($response);

		$parameter = new EndpointParameter('parameter1');
		$parameter->setDescription('description');
		$endpoint->addParameter($parameter);

		$parameter = new EndpointParameter('parameter2');
		$parameter->setDescription('description');
		$parameter->setRequired(false);
		$parameter->setAllowEmpty(true);
		$parameter->setDeprecated(true);
		$parameter->setIn('query');
		$endpoint->addParameter($parameter);

		$schema->addEndpoint($endpoint);

		$definition = new CoreDefinition($schema, new EntityAdapter());

		Assert::same(
			[
				'paths' => [
					'/foo/bar' => [
						'get' => [
							'tags' => ['tag1'],
							'requestBody' => ['content' => []],
							'responses' => [],
						],
						'post' => [
							'tags' => ['tag2', 'tag3'],
							'parameters' => [
								[
									'name' => 'parameter1',
									'in' => 'path',
									'description' => 'description',
									'required' => true,
									'schema' => ['type' => 'string'],
								],
								[
									'name' => 'parameter2',
									'in' => 'query',
									'description' => 'description',
									'required' => false,
									'schema' => ['type' => 'string'],
								],
							],
							'requestBody' => [
								'content' => [
									'application/json' => [
										'schema' => ['type' => 'object', 'properties' => ['int' => ['type' => 'integer']]],
									],
								],
								'required' => true,
								'description' => 'Description',
							],
							'responses' => [
								200 => [
									'description' => 'description',
									'content' => [
										'application/json' => ['schema' => ['type' => 'object', 'properties' => []]],
									],
								],
							],
							'description' => 'OpenApi description',
						],
						'put' => [
							'tags' => ['tag2', 'tag3'],
							'parameters' => [
								[
									'name' => 'parameter1',
									'in' => 'path',
									'description' => 'description',
									'required' => true,
									'schema' => ['type' => 'string'],
								],
								[
									'name' => 'parameter2',
									'in' => 'query',
									'description' => 'description',
									'required' => false,
									'schema' => ['type' => 'string'],
								],
							],
							'requestBody' => [
								'content' => [
									'application/json' => [
										'schema' => ['type' => 'object', 'properties' => ['int' => ['type' => 'integer']]],
									],
								],
								'required' => true,
								'description' => 'Description',
							],
							'responses' => [
								200 => [
									'description' => 'description',
									'content' => [
										'application/json' => ['schema' => ['type' => 'object', 'properties' => []]],
									],
								],
							],
							'description' => 'OpenApi description',
						],
					],
				],
				'info' => ['title' => 'Title', 'version' => '1.0.0'],
			],
			$definition->load()
		);
	}

	public function testParameterSchemaTypes(): void
	{
		$schema = new Schema();

		$endpoint = new Endpoint(new EndpointHandler('class', 'method'));
		$endpoint->setMask('/foo');
		$endpoint->setMethods(['GET']);

		$endpoint->addParameter(new EndpointParameter('date', EndpointParameter::TYPE_DATETIME));
		$endpoint->addParameter(new EndpointParameter('ratio', EndpointParameter::TYPE_FLOAT));
		$endpoint->addParameter(new EndpointParameter('count', EndpointParameter::TYPE_INTEGER));

		// Custom types come from CoreMappingPlugin and have no mapping of their own
		$endpoint->addParameter(new EndpointParameter('id', 'uuid'));

		$enumParameter = new EndpointParameter('state', EndpointParameter::TYPE_ENUM);
		$enumParameter->setEnum(['on', 'off']);
		$endpoint->addParameter($enumParameter);

		$schema->addEndpoint($endpoint);

		$definition = new CoreDefinition($schema, new EntityAdapter());

		$parameters = $definition->load()['paths']['/foo']['get']['parameters'];

		Assert::same(['type' => 'string', 'format' => 'date-time'], $parameters[0]['schema']);
		Assert::same(['type' => 'number'], $parameters[1]['schema']);
		Assert::same(['type' => 'integer'], $parameters[2]['schema']);
		Assert::same(['type' => 'string'], $parameters[3]['schema']);
		Assert::same(['type' => 'string', 'enum' => ['on', 'off']], $parameters[4]['schema']);
	}

}

(new CoreDefinitionTest())->run();
