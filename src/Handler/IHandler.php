<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IHandler
{

	/**
	 * @return mixed
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response);

}
