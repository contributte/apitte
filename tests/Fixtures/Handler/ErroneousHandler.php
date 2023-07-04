<?php declare(strict_types = 1);

namespace Tests\Fixtures\Handler;

use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use RuntimeException;

class ErroneousHandler implements IHandler
{

	public function handle(ApiRequest $request, ApiResponse $response): mixed
	{
		throw new RuntimeException(sprintf('I am %s!', self::class));
	}

}
