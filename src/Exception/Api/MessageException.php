<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Throwable;

class MessageException extends ClientErrorException
{

	public function __construct(string $errmessage = '', int $code = 500, ?Throwable $previous = null, ?string $message = null)
	{
		parent::__construct($errmessage, $code, $previous, $message);
	}

	/**
	 * @param string|string[] $message
	 */
	public function withMessage($message): self
	{
		parent::withMessage($message);
		$message = is_array($message) ? $message : [$message];

		return $this->withTypedContext('message', $message);
	}

}
