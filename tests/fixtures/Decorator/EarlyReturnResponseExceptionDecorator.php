<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EarlyReturnResponseExceptionDecorator implements IRequestDecorator, IResponseDecorator
{

	public function decorateRequest(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
	{
		throw new EarlyReturnResponseException($response);
	}

	public function decorateResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		throw new EarlyReturnResponseException($response);
	}

}
