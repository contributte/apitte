<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Throwable;

/**
 * Used for server errors (5xx)
 */
class ServerErrorException extends ApiException
{

	public function __construct(string $message = '', int $code = 500, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

}
