<?php declare(strict_types = 1);

namespace Tests;

use Apitte\Presenter\ApiRoute;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class ApiRouteTest extends BaseTestCase
{

	public function testConstruct(): void
	{
		$route = new ApiRoute('api');
		Assert::equal(['presenter' => 'Apitte:Api'], $route->getDefaults());
		Assert::equal('api/<path .*>', $route->getMask());
	}

}

(new ApiRouteTest())->run();
