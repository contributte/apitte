<?php declare(strict_types = 1);

namespace Apitte\Core\Router;

use Psr\Http\Message\ServerRequestInterface;

interface IRouter
{

	public function match(ServerRequestInterface $request): ?ServerRequestInterface;

}
