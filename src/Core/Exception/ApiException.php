<?php declare(strict_types = 1);

namespace Apitte\Core\Exception;

use Throwable;

abstract class ApiException extends RuntimeException
{

	use ExceptionExtra;

	public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null, mixed $context = null)
	{
		parent::__construct($message, $code, $previous);

		$this->context = $context;
	}

}
