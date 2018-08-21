<?php declare(strict_types = 1);

namespace Tests\Fixtures\Handler;

use Apitte\Core\Handler\IHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReturnFooBarHandler implements IHandler
{

	/**
	 * @return mixed[]
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response): array
	{
		return ['foo', 'bar'];
	}

}
