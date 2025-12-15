<?php declare(strict_types = 1);

namespace Tests\Cases\Core\LinkGenerator;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\LinkGenerator\LinkGenerator;
use Apitte\Core\LinkGenerator\LinkGeneratorException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Tester\Assert;
use Tester\TestCase;

final class LinkGeneratorTest extends TestCase
{

	public function testLinkByControllerMethod(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		$link = $linkGenerator->link('App\Controllers\UsersController::list');
		Assert::same('/api/users', $link);
	}

	public function testLinkByControllerMethodWithParams(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		$link = $linkGenerator->link('App\Controllers\UsersController::detail', ['id' => 123]);
		Assert::same('/api/users/123', $link);
	}

	public function testLinkById(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		$link = $linkGenerator->link('users.list');
		Assert::same('/api/users', $link);
	}

	public function testLinkByIdWithParams(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		$link = $linkGenerator->link('users.detail', ['id' => 456]);
		Assert::same('/api/users/456', $link);
	}

	public function testLinkWithQueryParams(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		$link = $linkGenerator->link('users.list', ['page' => 2, 'limit' => 10]);
		Assert::same('/api/users?page=2&limit=10', $link);
	}

	public function testLinkWithPathAndQueryParams(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		$link = $linkGenerator->link('users.detail', ['id' => 789, 'include' => 'posts']);
		Assert::same('/api/users/789?include=posts', $link);
	}

	public function testLinkNotFound(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		Assert::exception(
			fn () => $linkGenerator->link('nonexistent'),
			LinkGeneratorException::class,
			'Endpoint "nonexistent" not found'
		);
	}

	public function testLinkMissingRequiredParam(): void
	{
		$schema = $this->createSchema();
		$linkGenerator = new LinkGenerator($schema);

		Assert::exception(
			fn () => $linkGenerator->link('users.detail'),
			LinkGeneratorException::class,
			'Missing required parameter "id"'
		);
	}

	private function createSchema(): Schema
	{
		$schema = new Schema();

		// Users list endpoint
		$handler1 = new EndpointHandler('App\Controllers\UsersController', 'list');
		$endpoint1 = new Endpoint($handler1);
		$endpoint1->setMethods([Endpoint::METHOD_GET]);
		$endpoint1->setMask('/api/users');
		$endpoint1->addTag(Endpoint::TAG_ID, 'users.list');
		$schema->addEndpoint($endpoint1);

		// Users detail endpoint with path parameter
		$handler2 = new EndpointHandler('App\Controllers\UsersController', 'detail');
		$endpoint2 = new Endpoint($handler2);
		$endpoint2->setMethods([Endpoint::METHOD_GET]);
		$endpoint2->setMask('/api/users/{id}');
		$endpoint2->addTag(Endpoint::TAG_ID, 'users.detail');

		$idParam = new EndpointParameter('id', EndpointParameter::TYPE_INTEGER);
		$idParam->setIn(EndpointParameter::IN_PATH);
		$idParam->setRequired(true);
		$endpoint2->addParameter($idParam);

		$schema->addEndpoint($endpoint2);

		return $schema;
	}

}

(new LinkGeneratorTest())->run();
