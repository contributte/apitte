<?php declare(strict_types = 1);

namespace Apitte\Core\Exception;

use Exception;
use Throwable;

/**
 * @mixin Exception
 */
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
	 * @return static
	 */
	public function withCode(int $code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param string|string[] $message
	 * @return static
	 */
	public function withMessage($message)
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withPrevious(Throwable $exception)
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
	 * @param mixed $context
	 * @return static
	 */
	public function withTypedContext(string $type, $context)
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
