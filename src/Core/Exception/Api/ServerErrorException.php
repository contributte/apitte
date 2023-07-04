<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Throwable;

/**
 * Used for server errors (5xx)
 */
class ServerErrorException extends ApiException
{

	public static string $defaultMessage = 'Application encountered an internal error. Please try again later.';

	public function __construct(string $message = '', int $code = 500, ?Throwable $previous = null)
	{
		if ($code < 500 || $code > 599) {
			throw new InvalidArgumentException(sprintf('%s code could be only in range from 500 to 599', static::class));
		}

		parent::__construct($message !== '' ? $message : static::$defaultMessage, $code, $previous);
	}

}
