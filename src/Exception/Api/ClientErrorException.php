<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Throwable;

/**
 * Used for client/application errors (4xx)
 */
class ClientErrorException extends ApiException
{

	/**
	 * @param mixed $context
	 */
	public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null, $context = null)
	{
		parent::__construct($message, $code, $previous, $context);
	}

}
