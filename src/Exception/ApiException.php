<?php

namespace Apitte\Core\Exception;

use Exception;

abstract class ApiException extends RuntimeException
{

	use ExceptionExtra;

	/**
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 * @param mixed $context
	 */
	public function __construct($message = '', $code = 400, Exception $previous = NULL, $context = NULL)
	{
		parent::__construct($message, $code, $previous);
		$this->context = $context;
	}

}
