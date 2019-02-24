<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IRequestDecorator
{

	/**
	 * @param ApiRequest|ServerRequestInterface $request
	 * @param ApiResponse|ResponseInterface $response
	 * @return ApiRequest|ServerRequestInterface
	 * @throws EarlyReturnResponseException If other request decorators and also deeper layers (endpoint) should be skipped
	 */
	public function decorateRequest(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface;

}
