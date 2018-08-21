<?php declare(strict_types = 1);

namespace Tests\Fixtures\Dispatcher;

use Apitte\Core\Dispatcher\IDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FakeDispatcher implements IDispatcher
{

	public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		return $response;
	}

}
