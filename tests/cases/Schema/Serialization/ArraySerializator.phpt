<?php declare(strict_types = 1);

/**
 * Test: Schema\Serialization\ArraySerializator
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\Controller\MethodRequest;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Serialization\ArraySerializator;
use Contributte\Psr7\Psr7Response;
use Tester\Assert;

// Serialize: success
test(function (): void {
	$serializator = new ArraySerializator();

	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1-class');
	$c1->setId('c1-id');
	$c1->setPath('c1-path');
	$c1->addGroupId('c1-group-id');
	$c1->addGroupPath('group1-path');
	$c1->addGroupPath('group2-path');
	$c1->addTag('c1-t1', 'c1-t1-value');

	$c1->addMethod('m1'); // Skipped, missing path

	$m2 = $c1->addMethod('m2');
	$m2->addMethod(Endpoint::METHOD_GET);
	$m2->addMethod(Endpoint::METHOD_POST);
	$m2->addMethod(Endpoint::METHOD_PUT);
	$m2->setPath('m2-path');
	$m2->setRequestMapper('A\Class\Which\Implements\Apitte\Core\Mapping\Request\IRequestEntity', true);
	$m2->setResponseMapper('A\Class\Which\Implements\Apitte\Core\Mapping\Response\IResponseEntity');

	$m3 = $c1->addMethod('m3');
	$m3->setId('m3-id');
	$m3->addMethod(Endpoint::METHOD_GET);
	$m3->addMethod(Endpoint::METHOD_POST);
	$m3->setPath('m3-path/{m3-p1}');
	$m3->addTag('m3-t1');
	$m3->addTag('m3-t2', 'm3-t2-value');
	$m3->setDescription('m3-description');
	$m3->addArgument('m3-a1', Psr7Response::class);

	$m3r = new MethodRequest();
	$m3r->setRequired(true);
	$m3r->setEntity('SomeClass');
	$m3->setRequest($m3r);

	$m3->addResponse('200', 'Success')
		->setEntity('SomeClass[]');
	$m3->addResponse('404', 'Not found');

	$m3->setOpenApi(['openapi' => '3.0.2']);

	$m3n1 = $m3->addNegotiation('json');
	$m3n1->setDefault(true);
	$m3n1->setRenderer('A\\Middleware\\Implementing\\Class');

	$m3n2 = $m3->addNegotiation('xml');
	$m3n2->setDefault(true);

	$m3p1 = $m3->addParameter('m3-p1', EndpointParameter::TYPE_INTEGER);
	$m3p1->setDescription('m3-p1-desc');
	$m3p1->setIn(EndpointParameter::IN_PATH);
	$m3p1->setRequired(true);
	$m3p1->setAllowEmpty(true);
	$m3p1->setDeprecated(true);

	$m3p2 = $m3->addParameter('m3-p2', EndpointParameter::TYPE_STRING);
	$m3p2->setIn(EndpointParameter::IN_QUERY);

	$expected = [
		[
			'handler' => ['class' => 'c1-class', 'method' => 'm2', 'arguments' => []],
			'id' => null,
			'tags' => ['c1-t1' => 'c1-t1-value'],
			'methods' => ['GET', 'POST', 'PUT'],
			'mask' => '/group1-path/group2-path/c1-path/m2-path',
			'description' => null,
			'parameters' => [],
			'responses' => [],
			'negotiations' => [],
			'attributes' => ['pattern' => '/group1-path/group2-path/c1-path/m2-path'],
			'openApi' => ['controller' => [], 'method' => []],
			'requestMapper' => [
				'entity' => 'A\\Class\\Which\\Implements\\Apitte\\Core\\Mapping\\Request\\IRequestEntity',
				'validation' => true,
			],
			'responseMapper' => [
				'entity' => 'A\\Class\\Which\\Implements\\Apitte\\Core\\Mapping\\Response\\IResponseEntity',
			],
		],
		[
			'handler' => [
				'class' => 'c1-class',
				'method' => 'm3',
				'arguments' => ['m3-a1' => 'Contributte\\Psr7\\Psr7Response'],
			],
			'id' => 'c1-group-id.c1-id.m3-id',
			'tags' => ['c1-t1' => 'c1-t1-value', 'm3-t1' => null, 'm3-t2' => 'm3-t2-value'],
			'methods' => ['GET', 'POST'],
			'mask' => '/group1-path/group2-path/c1-path/m3-path/{m3-p1}',
			'description' => 'm3-description',
			'parameters' => [
				'm3-p1' => [
					'name' => 'm3-p1',
					'type' => 'int',
					'description' => 'm3-p1-desc',
					'in' => 'path',
					'required' => true,
					'allowEmpty' => true,
					'deprecated' => true,
				],
				'm3-p2' => [
					'name' => 'm3-p2',
					'type' => 'string',
					'description' => null,
					'in' => 'query',
					'required' => true,
					'allowEmpty' => false,
					'deprecated' => false,
				],
			],
			'responses' => [
				200 => [
					'code' => '200',
					'description' => 'Success',
					'entity' => 'SomeClass[]',
				],
				404 => ['code' => '404', 'description' => 'Not found'],
			],
			'negotiations' => [
				[
					'suffix' => 'json',
					'default' => true,
					'renderer' => 'A\\Middleware\\Implementing\\Class',
				],
				[
					'suffix' => 'xml',
					'default' => true,
					'renderer' => null,
				],
			],
			'attributes' => [
				'pattern' => '/group1-path/group2-path/c1-path/m3-path/(?P<m3-p1>[^/]+)',
			],
			'openApi' => ['controller' => [], 'method' => ['openapi' => '3.0.2']],
			'request' => ['required' => true, 'entity' => 'SomeClass'],
		],
	];

	Assert::same($expected, $serializator->serialize($builder));
});

// Serialize: Exception - duplicate mask parameter - in controller
test(function (): void {
	$serializator = new ArraySerializator();

	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1-class');
	$c1->setId('c1-id');
	$c1->setPath('{c1-p1}/{c1-p1}');

	// Only pairs Controller + Method are validated, so Method must be defined
	$m1 = $c1->addMethod('m1');
	$m1->setPath('{m1-p1}');

	$m1->addParameter('m1-p1', EndpointParameter::IN_PATH);

	Assert::exception(function () use ($serializator, $builder): void {
		$serializator->serialize($builder);
	}, InvalidStateException::class, 'Duplicate mask parameter "c1-p1" in path "/{c1-p1}/{c1-p1}/{m1-p1}"');
});

// Serialize: Exception - duplicate mask parameter - in method
test(function (): void {
	$serializator = new ArraySerializator();

	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1-class');
	$c1->setId('c1-id');
	$c1->setPath('{c1-p1}');

	$m1 = $c1->addMethod('m1');
	$m1->setPath('{m1-p1}/{m1-p1}');

	Assert::exception(function () use ($serializator, $builder): void {
		$serializator->serialize($builder);
	}, InvalidStateException::class, 'Duplicate mask parameter "m1-p1" in path "/{c1-p1}/{m1-p1}/{m1-p1}"');
});

// Serialize: Exception - Parameter in mask is not defined in path
test(function (): void {
	$serializator = new ArraySerializator();

	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1-class');
	$c1->setId('c1-id');

	$m1 = $c1->addMethod('m1');
	$m1->setPath('path');

	$m1->addParameter('m1-p1', EndpointParameter::IN_PATH);

	Assert::exception(function () use ($serializator, $builder): void {
		$serializator->serialize($builder);
	}, InvalidStateException::class, '@RequestParameter(name="m1-p1", in=path) is not defined in mask (in @GroupPath, @ControllerPath or @Path)');
});
