<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface IErrorHandler
{

	/**
	 * Log error and generate response
	 */
	public function handle(Throwable $error): ResponseInterface;

}
