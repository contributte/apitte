<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Http\ApiResponse;
use Throwable;

interface IErrorHandler
{

	/**
	 * Log error and generate response
	 */
	public function handle(Throwable $error): ApiResponse;

}
