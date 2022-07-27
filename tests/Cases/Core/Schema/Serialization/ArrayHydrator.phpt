<?php declare(strict_types = 1);

/**
 * Test: Schema\Serialization\ArrayHydrator
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Tester\Assert;

// AddMethod: success
test(function (): void {
	$hydrator = new ArrayHydrator();

	$data = [
		[
			'handler' => ['class' => 'c1-class', 'method' => 'm2', 'arguments' => []],
			'id' => null,
			'tags' => ['c1-t1' => 'c1-t1-value'],
			'methods' => ['GET', 'POST', 'PUT'],
			'mask' => '/group1-path/group2-path/c1-path/m2-path',
			'parameters' => [],
			'negotiations' => [],
			'attributes' => ['pattern' => '/group1-path/group2-path/c1-path/m2-path'],
			'requestBody' => [
				'description' => 'description',
				'required' => true,
				'validation' => false,
				'entity' => 'A\Class\Which\Implements\Apitte\Core\Mapping\Request\IRequestEntity',
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
		],
	];

	$schema = $hydrator->hydrate($data);

	$endpoints = $schema->getEndpoints();
	Assert::count(2, $endpoints);

	// Endpoint 1
	$endpoint1 = $endpoints[0];
	Assert::same([Endpoint::METHOD_GET, Endpoint::METHOD_POST, Endpoint::METHOD_PUT], $endpoint1->getMethods());
	Assert::same('/group1-path/group2-path/c1-path/m2-path', $endpoint1->getMask());
	Assert::same('/group1-path/group2-path/c1-path/m2-path', $endpoint1->getAttribute('pattern'));
	Assert::same(null, $endpoint1->getAttribute('missing'));
	Assert::same('#^/group1-path/group2-path/c1-path/m2-path$#', $endpoint1->getPattern());
	Assert::same([], $endpoint1->getParameters());
	Assert::same([], $endpoint1->getNegotiations());

	$requestBody1 = $endpoint1->getRequestBody();
	Assert::same('A\Class\Which\Implements\Apitte\Core\Mapping\Request\IRequestEntity', $requestBody1->getEntity());
	Assert::same(false, $requestBody1->isValidation());
	Assert::same(true, $requestBody1->isRequired());
	Assert::same('description', $requestBody1->getDescription());

	Assert::same(['c1-t1' => 'c1-t1-value'], $endpoint1->getTags());
	Assert::same('c1-t1-value', $endpoint1->getTag('c1-t1'));

	$handler1 = $endpoint1->getHandler();
	Assert::same('c1-class', $handler1->getClass());
	Assert::same('m2', $handler1->getMethod());

	// Endpoint 2
	$endpoint2 = $endpoints[1];
	Assert::same([Endpoint::METHOD_GET, Endpoint::METHOD_POST], $endpoint2->getMethods());
	Assert::same('/group1-path/group2-path/c1-path/m3-path/{m3-p1}', $endpoint2->getMask());
	Assert::same('/group1-path/group2-path/c1-path/m3-path/(?P<m3-p1>[^/]+)', $endpoint2->getAttribute('pattern'));
	Assert::same(null, $endpoint2->getAttribute('missing'));
	Assert::same('#^/group1-path/group2-path/c1-path/m3-path/(?P<m3-p1>[^/]+)(json|xml)?$#U', $endpoint2->getPattern());

	Assert::same(null, $endpoint2->getRequestBody());

	Assert::same(['c1-t1' => 'c1-t1-value', 'm3-t1' => null, 'm3-t2' => 'm3-t2-value', 'id' => 'c1-group-id.c1-id.m3-id'], $endpoint2->getTags());
	Assert::same('c1-t1-value', $endpoint2->getTag('c1-t1'));

	$handler2 = $endpoint2->getHandler();
	Assert::same('c1-class', $handler2->getClass());
	Assert::same('m3', $handler2->getMethod());

	$parameters2 = $endpoint2->getParameters();
	Assert::count(2, $parameters2);
	Assert::same(EndpointParameter::IN_PATH, $parameters2['m3-p1']->getIn());
	Assert::same(EndpointParameter::TYPE_INTEGER, $parameters2['m3-p1']->getType());
	Assert::same('m3-p1', $parameters2['m3-p1']->getName());
	Assert::same('m3-p1-desc', $parameters2['m3-p1']->getDescription());
	Assert::same(true, $parameters2['m3-p1']->isAllowEmpty());
	Assert::same(true, $parameters2['m3-p1']->isDeprecated());
	Assert::same(true, $parameters2['m3-p1']->isRequired());

	Assert::same(EndpointParameter::IN_QUERY, $parameters2['m3-p2']->getIn());
	Assert::same(EndpointParameter::TYPE_STRING, $parameters2['m3-p2']->getType());
	Assert::same('m3-p2', $parameters2['m3-p2']->getName());
	Assert::same(null, $parameters2['m3-p2']->getDescription());
	Assert::same(false, $parameters2['m3-p2']->isAllowEmpty());
	Assert::same(false, $parameters2['m3-p2']->isDeprecated());
	Assert::same(true, $parameters2['m3-p2']->isRequired());

	$parameters2inPath = $endpoint2->getParametersByIn(EndpointParameter::IN_PATH);
	Assert::count(1, $parameters2inPath);
	Assert::same('m3-p1', $parameters2inPath['m3-p1']->getName());

	$negotiations2 = $endpoint2->getNegotiations();
	Assert::count(2, $negotiations2);
	Assert::same('A\Middleware\Implementing\Class', $negotiations2[0]->getRenderer());
	Assert::same('json', $negotiations2[0]->getSuffix());
	Assert::same(true, $negotiations2[0]->isDefault());

	Assert::same(null, $negotiations2[1]->getRenderer());
	Assert::same('xml', $negotiations2[1]->getSuffix());
	Assert::same(true, $negotiations2[1]->isDefault());
});
