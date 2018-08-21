<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EarlyReturnResponseExceptionDecorator implements IDecorator
{

	/**
	 * @param mixed[] $context
	 */
	public function decorate(ServerRequestInterface $request, ResponseInterface $response, array $context = []): void
	{
		throw new EarlyReturnResponseException($response);
	}

}
