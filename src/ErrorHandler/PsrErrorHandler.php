<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Utils\Helpers;
use Psr\Log\LoggerInterface;
use Throwable;

class PsrErrorHandler implements IErrorHandler
{

	/** @var LoggerInterface */
	private $logger;

	/** @var bool */
	private $catchException = false;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function setCatchException(bool $catchException): void
	{
		$this->catchException = $catchException;
	}

	public function handle(Throwable $throwable): void
	{
		// Log exception
		$this->logger->error($throwable->getMessage(), ['exception' => Helpers::throwableToArray($throwable)]);

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
