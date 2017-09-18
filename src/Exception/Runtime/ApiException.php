<?php

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{

	/** @var mixed */
	protected $context;

	/**
	 * @param string $message
	 * @param int $code
	 * @param Throwable|NULL $previous
	 * @param mixed $context
	 */
	public function __construct($message = '', $code = 0, Throwable $previous = NULL, $context = NULL)
	{
		parent::__construct($message, $code, $previous);
		$this->context = $context;
	}

	/**
	 * @return mixed
	 */
	public function getContext()
	{
		return $this->context;
	}

}
