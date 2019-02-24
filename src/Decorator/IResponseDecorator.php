<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IResponseDecorator
{

	/**
	 * @param ApiRequest|ServerRequestInterface $request
	 * @param ApiResponse|ResponseInterface     $response
	 * @return ApiResponse|ResponseInterface $response
	 * @throws EarlyReturnResponseException If other response decorators should be skipped
	 */
	public function decorateResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

}
