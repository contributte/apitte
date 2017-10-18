<?php

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Exception;

/**
 * Used for client/application errors (4xx)
 */
class ClientErrorException extends ApiException
{

	/**
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 * @param mixed $context
	 */
	public function __construct($message = '', $code = 400, Exception $previous = NULL, $context = NULL)
	{
		parent::__construct($message, $code, $previous, $context);
	}

}
