<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ReturnResponseDecorator implements IResponseDecorator, IErrorDecorator
{

	public function decorateResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		return $response;
	}

	public function decorateError(ServerRequestInterface $request, ResponseInterface $response, Throwable $error): ResponseInterface
	{
		return $response;
	}

}
