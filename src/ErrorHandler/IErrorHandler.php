<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Throwable;

interface IErrorHandler
{

	public function setCatchException(bool $catchException): void;

	/**
	 * Log error and rethrow if it should not be catch
	 *
	 * @throws Throwable
	 */
	public function handle(Throwable $throwable): void;

}
