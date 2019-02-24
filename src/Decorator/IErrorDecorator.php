<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface IErrorDecorator
{

	/**
	 * @param ApiRequest|ServerRequestInterface $request
	 * @param ApiResponse|ResponseInterface     $response
	 * @return ApiResponse|ResponseInterface    $response
	 */
	public function decorateError(ServerRequestInterface $request, ResponseInterface $response, Throwable $error): ResponseInterface;

}
