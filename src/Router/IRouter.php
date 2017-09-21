<?php

namespace Apitte\Core\Router;

use Psr\Http\Message\ServerRequestInterface;

interface IRouter
{

	/**
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface|NULL
	 */
	public function match(ServerRequestInterface $request);

}
