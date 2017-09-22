<?php

namespace Apitte\Core\Exception;

use Exception;

trait ExceptionExtra
{

	/** @var mixed */
	protected $context;

	/**
	 * @return static
	 */
	public static function create()
	{
		return new static();
	}

	/**
	 * @param int $code
	 * @return static
	 */
	public function withCode($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param string $message
	 * @return static
	 */
	public function withMessage($message)
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * @param Exception $exception
	 * @return static
	 */
	public function withPrevious(Exception $exception)
	{
		$this->previous = $exception;

		return $this;
	}

	/**
	 * @param mixed $context
	 * @return static
	 */
	public function withContext($context)
	{
		$this->context = $context;

		return $this;
	}

	/**
	 * @param string $type
	 * @param mixed $context
	 * @return static
	 */
	public function withTypedContext($type, $context)
	{
		$this->context = [$type => $context];

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getContext()
	{
		return $this->context;
	}

}
