<?php declare(strict_types = 1);

namespace Tests\Fixtures\Handler;

use Apitte\Core\Handler\IHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FakeNullHandler implements IHandler
{

	/**
	 * @return null
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		return null;
	}

}
