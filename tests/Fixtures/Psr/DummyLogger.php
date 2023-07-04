<?php declare(strict_types = 1);

namespace Tests\Fixtures\Psr;

use Psr\Log\AbstractLogger;

class DummyLogger extends AbstractLogger
{

	/** @var mixed[] */
	public array $records = [];

	/**
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
	 */
	public function log($level, $message, array $context = []): void
	{
		$record = [
			'level' => $level,
			'message' => $message,
			'context' => $context,
		];

		$this->records[] = $record;
	}

}
