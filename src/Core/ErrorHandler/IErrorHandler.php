<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\Http\ApiResponse;

interface IErrorHandler
{

	/**
	 * Log error and generate response
	 */
	public function handle(DispatchError $dispatchError): ApiResponse;

}
