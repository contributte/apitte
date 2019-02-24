<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RethrowErrorDecorator implements IErrorDecorator
{

	public function decorateError(ServerRequestInterface $request, ResponseInterface $response, Throwable $error): ResponseInterface
	{
		throw $error;
	}

}
