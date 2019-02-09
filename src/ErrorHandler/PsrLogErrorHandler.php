<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Psr\Log\LoggerInterface;
use Throwable;

class PsrErrorHandler extends SimpleErrorHandler
{

	/** @var LoggerInterface */
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function handle(Throwable $throwable): void
	{
		$this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

		parent::handle($throwable);
	}

}
