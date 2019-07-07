<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiResponse;
use Psr\Log\LoggerInterface;
use Throwable;

class PsrLogErrorHandler extends SimpleErrorHandler
{

	/** @var LoggerInterface */
	private $logger;

	public function __construct(ErrorConverter $errorConverter, LoggerInterface $logger)
	{
		parent::__construct($errorConverter);
		$this->logger = $logger;
	}

	public function handle(Throwable $error): ApiResponse
	{
		// Log exception only if it's not designed to be displayed
		if (!($error instanceof ApiException)) {
			$this->logger->error($error->getMessage(), ['exception' => $error]);
		}

		// Also log original exception if any
		if ($error instanceof ApiException && $error->getPrevious() !== null) {
			$this->logger->error($error->getMessage(), ['exception' => $error->getPrevious()]);
		}

		return parent::handle($error);
	}

}
