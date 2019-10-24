<?php declare(strict_types = 1);

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\Hierarchy\ControllerMethodPair;
use Apitte\Core\Schema\Hierarchy\HierarchyBuilder;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Utils\Helpers;
use Tester\Assert;
use Tester\TestCase;

class SchemaBuilderTest extends TestCase
{

	public function testCorrectOrder(): void
	{
		$builder = new SchemaBuilder();

		$this->addGetAlphabeticallyFirstEndpoint($builder);
		$this->addGetUsersSomethingEndpoint($builder);
		$this->addGetUsersIdEndpoint($builder);
		$this->addPutPatchUsersIdEndpoint($builder);
		$this->addGetUsersEndpoint($builder);
		$this->addGetIdUsersEndpoint($builder);
		$this->addCombinedRootEndpoint($builder);

		$hierarchyBuilder = new HierarchyBuilder($builder->getControllers());
		$this->makeAssertions($hierarchyBuilder);
	}

	public function testReverseOrder(): void
	{
		$builder = new SchemaBuilder();

		$this->addCombinedRootEndpoint($builder);
		$this->addGetIdUsersEndpoint($builder);
		$this->addGetUsersEndpoint($builder);
		$this->addPutPatchUsersIdEndpoint($builder);
		$this->addGetUsersIdEndpoint($builder);
		$this->addGetUsersSomethingEndpoint($builder);
		$this->addGetAlphabeticallyFirstEndpoint($builder);

		$hierarchyBuilder = new HierarchyBuilder($builder->getControllers());
		$this->makeAssertions($hierarchyBuilder);
	}

	public function testShuffledOrder(): void
	{
		$builder = new SchemaBuilder();

		$this->addGetUsersEndpoint($builder);
		$this->addPutPatchUsersIdEndpoint($builder);
		$this->addGetUsersIdEndpoint($builder);
		$this->addCombinedRootEndpoint($builder);
		$this->addGetAlphabeticallyFirstEndpoint($builder);
		$this->addGetUsersSomethingEndpoint($builder);
		$this->addGetIdUsersEndpoint($builder);

		$hierarchyBuilder = new HierarchyBuilder($builder->getControllers());
		$this->makeAssertions($hierarchyBuilder);
	}

	private function makeAssertions(HierarchyBuilder $builder): void
	{
		$endpoints = $builder->getSortedEndpoints();

		// Ensure all endpoints are tested - always number is incremented an assertion should be added
		Assert::count(7, $endpoints);

		// Independently on order in which are endpoints added they should be outputted always in predefined order
		Assert::same('GET /alphabetically-first', $this->buildPath($endpoints[0]));
		Assert::same('GET /users/something', $this->buildPath($endpoints[1]));
		Assert::same('GET /users/{id}', $this->buildPath($endpoints[2]));
		Assert::same('PUT, PATCH /users/{id}', $this->buildPath($endpoints[3]));
		Assert::same('GET /users', $this->buildPath($endpoints[4]));
		Assert::same('GET /{id}/users', $this->buildPath($endpoints[5]));
		Assert::same('GET, POST, PUT, DELETE, OPTIONS, PATCH /', $this->buildPath($endpoints[6]));
	}

	private function addGetUsersSomethingEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('getUsersSomething');
		$controller->setPath('users');
		$method = $controller->addMethod('getUsersSomething');
		$method->setPath('something');
		$method->setHttpMethods([Endpoint::METHOD_GET]);
	}

	private function addPutPatchUsersIdEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('putPatchUsersId');
		$controller->setPath('users');
		$method = $controller->addMethod('putPatchUsersId');
		$method->setPath('{id}');
		$method->setHttpMethods([Endpoint::METHOD_PUT, Endpoint::METHOD_PATCH]);
	}

	private function addGetUsersIdEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('getUsersId');
		$controller->setPath('users');
		$method = $controller->addMethod('getUsersId');
		$method->setPath('{id}');
		$method->setHttpMethods([Endpoint::METHOD_GET]);
	}

	private function addGetAlphabeticallyFirstEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('alphabeticallyFirst');
		$controller->setPath('alphabetically-first');
		$method = $controller->addMethod('alphabeticallyFirst');
		$method->setPath('');
		$method->setHttpMethods([Endpoint::METHOD_GET]);
	}

	private function addGetUsersEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('getUsers');
		$controller->setPath('users');
		$method = $controller->addMethod('getUsers');
		$method->setPath('');
		$method->setHttpMethods([Endpoint::METHOD_GET]);
	}

	private function addGetIdUsersEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('getIdUsers');
		$controller->setPath('{id}');
		$method = $controller->addMethod('getIdUsers');
		$method->setPath('users');
		$method->setHttpMethods([Endpoint::METHOD_GET]);
	}

	private function addCombinedRootEndpoint(SchemaBuilder $builder): void
	{
		$controller = $builder->addController('combinedRoot');
		$controller->setPath('');
		$method = $controller->addMethod('combinedRoot');
		$method->setPath('');
		$method->setHttpMethods(Endpoint::METHODS);
	}

	private function buildPath(ControllerMethodPair $endpoint): string
	{
		$controller = $endpoint->getController();
		$method = $endpoint->getMethod();

		$pathsList = array_merge(
			$controller->getGroupPaths(),
			[$controller->getPath()],
			[$method->getPath()]
		);
		$path = implode('/', $pathsList);
		$path = Helpers::slashless($path);
		$path = '/' . trim($path, '/');

		return implode(', ', $method->getHttpMethods()) . ' ' . $path;
	}

}

(new SchemaBuilderTest())->run();
