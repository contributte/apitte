<?php declare(strict_types = 1);

namespace Tests\Fixtures\Utils;

use Psr\Log\LoggerInterface;

class FakeLogger implements LoggerInterface
{

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function emergency($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function alert($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function critical($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function error($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function warning($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function notice($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function info($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function debug($message, array $context = []): void
	{
		// Noop
	}

	/**
	 * @param int $level
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function log($level, $message, array $context = []): void
	{
		// Noop
	}

}
