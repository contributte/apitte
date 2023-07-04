<?php declare(strict_types = 1);

namespace Tests\Cases\Presenter;

use Apitte\Presenter\ApiRoute;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class ApiRouteTest extends TestCase
{

	public function testConstruct(): void
	{
		$route = new ApiRoute('api');
		Assert::equal(['presenter' => 'Apitte:Api'], $route->getDefaults());
		Assert::equal('api/<path .*>', $route->getMask());
	}

}

(new ApiRouteTest())->run();
