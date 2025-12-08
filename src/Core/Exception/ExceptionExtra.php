<?php declare(strict_types = 1);

namespace Apitte\Core\Exception;

use Exception;
use ReflectionClass;
use Throwable;

/**
 * @mixin Exception
 */
trait ExceptionExtra
{

	protected mixed $context = null;

	/**
	 * @return static
	 */
	public static function create(): static
	{
		return new static();
	}

	/**
	 * @return static
	 */
	public function withCode(int $code): static
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param string|string[] $message
	 * @return static
	 */
	public function withMessage(string|array $message): static
	{
		$this->message = is_array($message) ? implode(';', $message) : $message;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withPrevious(Throwable $exception): static
	{
		// @phpcs:ignore SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
		$reflection = new ReflectionClass(Exception::class);
		$property = $reflection->getProperty('previous');
		$property->setValue($this, $exception);

		return $this;
	}

	/**
	 * @return static
	 */
	public function withContext(mixed $context): static
	{
		$this->context = $context;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withTypedContext(string $type, mixed $context): static
	{
		$this->context = [$type => $context];

		return $this;
	}

	public function getContext(): mixed
	{
		return $this->context;
	}

}
