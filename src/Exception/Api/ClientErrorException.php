<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Logical\InvalidArgumentException;
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
		if ($code < 400 || $code > 499) {
			throw new InvalidArgumentException(sprintf('%s code could be only in range from 400 to 499', static::class));
		}

		parent::__construct($message, $code, $previous, $context);
	}

}
