<?php declare(strict_types = 1);

namespace Tests\Fixtures\Psr;

use Psr\Log\AbstractLogger;
use Stringable;

class DummyLogger extends AbstractLogger
{

	public array $records = [];

	/**
	 * @param mixed $level
	 * @param string|Stringable $message
	 * @param mixed[] $context
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
