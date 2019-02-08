<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\Runtime\SnapshotException;
use Throwable;

class SimpleErrorHandler implements IErrorHandler
{

	/** @var bool */
	private $catchException = false;

	public function setCatchException(bool $catchException): void
	{
		$this->catchException = $catchException;
	}

	public function handle(Throwable $throwable): void
	{
		// Rethrow error if it should not be catch (debug only)
		if (!$this->catchException) {

			// Unwrap exception from snapshot
			if ($throwable instanceof SnapshotException) {
				throw $throwable->getPrevious();
			}

			throw $throwable;
		}
	}

}
