<?php declare(strict_types = 1);

namespace Tests\Fixtures\Psr;

use Psr\Log\AbstractLogger;
use Stringable;

class DummyLogger extends AbstractLogger
{

	/** @var mixed[] */
	public array $records = [];

	/**
	 * @param mixed[] $context
	 */
	public function log(mixed $level, string|Stringable $message, array $context = []): void
	{
		$record = [
			'level' => $level,
			'message' => $message,
			'context' => $context,
		];

		$this->records[] = $record;
	}

}
