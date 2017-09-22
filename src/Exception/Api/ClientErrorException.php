<?php

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Throwable;

/**
 * Used for client/application errors (4xx)
 */
class ClientErrorException extends ApiException
{

	/**
	 * @param string $message
	 * @param int $code
	 * @param Throwable $previous
	 * @param mixed $context
	 */
	public function __construct($message = '', $code = 400, Throwable $previous = NULL, $context = NULL)
	{
		parent::__construct($message, $code, $previous, $context);
	}

}
