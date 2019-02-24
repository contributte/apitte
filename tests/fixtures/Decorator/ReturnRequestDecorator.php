<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IRequestDecorator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReturnRequestDecorator implements IRequestDecorator
{

	public function decorateRequest(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
	{
		return $request;
	}

}
