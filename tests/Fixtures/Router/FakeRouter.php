<?php declare(strict_types = 1);

namespace Tests\Fixtures\Router;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Router\IRouter;

class FakeRouter implements IRouter
{

	/** @var bool */
	private $match;

	public function __construct(bool $match)
	{
		$this->match = $match;
	}

	public function match(ApiRequest $request): ?ApiRequest
	{
		if ($this->match) {
			return $request;
		}

		return null;
	}

}
