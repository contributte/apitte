<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Throwable;
use Tracy\ILogger;

class TracyErrorHandler extends SimpleErrorHandler
{

	/** @var ILogger */
	private $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

	public function handle(Throwable $throwable): void
	{
		$this->logger->log($throwable, ILogger::EXCEPTION);

		parent::handle($throwable);
	}

}
