<?php declare(strict_types = 1);

namespace Tests\Fixtures\Router;

use Apitte\Core\Router\IRouter;
use Psr\Http\Message\ServerRequestInterface;

class FakeRouter implements IRouter
{

	/** @var bool */
	private $match;

	public function __construct(bool $match)
	{
		$this->match = $match;
	}

	public function match(ServerRequestInterface $request): ?ServerRequestInterface
	{
		if ($this->match) {
			return $request;
		}
		return null;
	}

}
