<?php

namespace Apitte\Core\Exception;

use Throwable;

abstract class ApiException extends RuntimeException
{

	use ExceptionExtra;

	/**
	 * @param string $message
	 * @param int $code
	 * @param Throwable $previous
	 * @param mixed $context
	 */
	public function __construct($message = '', $code = 400, Throwable $previous = NULL, $context = NULL)
	{
		parent::__construct($message, $code, $previous);
		$this->context = $context;
	}

}
