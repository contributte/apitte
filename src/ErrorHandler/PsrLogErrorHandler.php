<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

class PsrLogErrorHandler extends SimpleErrorHandler
{

	/** @var LoggerInterface */
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function handle(Throwable $error): ApiResponse
	{
		// Unwrap error from snapshot for logging
		$originalError = $error;

		if ($error instanceof SnapshotException) {
			$error = $error->getPrevious();
		}

		// Log exception only if it's not designed to be displayed
		if (!$error instanceof ApiException) {
			$this->logger->error($error->getMessage(), ['exception' => $error]);
		}

		// Also log original exception if any
		if ($error instanceof ApiException && ($previous = $error->getPrevious()) !== null) {
			// Server error is expected to contain a real error while client error can contain just information, why client request failed
			$level = $error instanceof ServerErrorException ? LogLevel::ERROR : LogLevel::DEBUG;
			$this->logger->log($level, $previous->getMessage(), ['exception' => $previous]);
		}

		return parent::handle($originalError);
	}

}
