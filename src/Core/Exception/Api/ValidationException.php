<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Api;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Throwable;

class ValidationException extends ClientErrorException
{

	/**
	 * @param mixed[] $fields
	 */
	public function __construct(string $message = 'Request body contains an error. See context for details.', int $code = 422, ?Throwable $previous = null, array $fields = [])
	{
		parent::__construct($message, $code, $previous, $fields);
	}

	/**
	 * @param mixed[] $fields
	 * @return static
	 */
	public function withFields(array $fields): static
	{
		return $this->withTypedContext('validation', $fields);
	}

	/**
	 * @param mixed[] $fields
	 * @return static
	 */
	public function withFormFields(array $fields): static
	{
		foreach ($fields as $key => $value) {
			if (is_numeric($key)) {
				throw new InvalidArgumentException(sprintf('Field key must be string "%s" given.', (string) $key));
			}

			if (!is_array($value)) {
				throw new InvalidArgumentException(sprintf('Field values must be array, %s given.', get_debug_type($value)));
			}
		}

		return $this->withTypedContext('validation', $fields);
	}

}
