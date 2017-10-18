<?php

namespace Apitte\Core\Exception\Api;

use Exception;

class MessageException extends ClientErrorException
{

	/**
	 * @param string $errmessage
	 * @param int $code
	 * @param Exception $previous
	 * @param string $message
	 */
	public function __construct($errmessage = '', $code = 500, Exception $previous = NULL, $message = NULL)
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
