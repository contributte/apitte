<?php

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Throwable;

/**
 * Used for server errors (5xx)
 */
class ServerErrorException extends ApiException
{

	/**
	 * @param string $message
	 * @param int $code
	 * @param Throwable $previous
	 */
	public function __construct($message = '', $code = 500, Throwable $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
	}

}
