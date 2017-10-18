<?php

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Exception;

/**
 * Used for server errors (5xx)
 */
class ServerErrorException extends ApiException
{

	/**
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 */
	public function __construct($message = '', $code = 500, Exception $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
	}

}
