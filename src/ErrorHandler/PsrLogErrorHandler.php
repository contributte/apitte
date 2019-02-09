<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class PsrLogErrorHandler extends SimpleErrorHandler
{

	/** @var LoggerInterface */
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function handle(Throwable $error): ResponseInterface
	{
		// Log exception only if it's not designed to be displayed
		if (!($error instanceof ApiException)) {
			$this->logger->error($error->getMessage(), ['exception' => $error]);
		}

		return parent::handle($error);
	}

}
