<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Throwable;

class ValidationException extends ClientErrorException
{

	/**
	 * @param mixed[] $fields
	 */
	public function __construct(string $message = 'Validation error. See details in the context.', int $code = 422, ?Throwable $previous = null, array $fields = [])
	{
		parent::__construct($message, $code, $previous, $fields);
	}

	/**
	 * @param mixed[] $fields
	 * @return static
	 */
	public function withFields(array $fields)
	{
		return $this->withTypedContext('validation', $fields);
	}

	/**
	 * @param mixed[] $fields
	 * @return static
	 */
	public function withFormFields(array $fields)
	{
		foreach ($fields as $key => $value) {
			if (is_numeric($key)) throw new InvalidArgumentException(sprintf('Field key must be string "%s" give.', $key));
			if (!is_array($value)) throw new InvalidArgumentException(sprintf('Field values must be array "%s" give.', $value));
		}

		return $this->withTypedContext('validation', $fields);
	}

}
