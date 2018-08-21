<?php declare(strict_types = 1);

namespace Tests\Fixtures\Handler;

use Apitte\Core\Handler\IHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class ErroneousHandler implements IHandler
{

	public function handle(ServerRequestInterface $request, ResponseInterface $response): void
	{
		throw new RuntimeException(sprintf('I am %s!', self::class));
	}

}
