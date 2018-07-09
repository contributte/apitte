<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IDispatcher
{

	public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ?ResponseInterface;

}
