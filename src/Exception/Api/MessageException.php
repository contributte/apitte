<?php

namespace Apitte\Core\Exception\Api;

use Throwable;

class MessageException extends ClientErrorException
{

	/**
	 * @param string $errmessage
	 * @param int $code
	 * @param Throwable $previous
	 * @param string $message
	 */
	public function __construct($errmessage = '', $code = 500, Throwable $previous = NULL, $message = NULL)
	{
		parent::__construct($errmessage, $code, $previous, $message);
	}

	/**
	 * @param string $message
	 * @return static
	 */
	public function withMessage($message)
	{
		parent::withMessage($message);
		$message = is_array($message) ? $message : [$message];

		return $this->withTypedContext('message', $message);
	}

}
