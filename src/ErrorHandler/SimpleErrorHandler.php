<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiResponse;
use Throwable;

class SimpleErrorHandler implements IErrorHandler
{

	/** @var ErrorConverter */
	private $errorConverter;

	/** @var bool */
	private $catchException = false;

	public function __construct(ErrorConverter $errorConverter)
	{
		$this->errorConverter = $errorConverter;
	}

	public function setCatchException(bool $catchException): void
	{
		$this->catchException = $catchException;
	}

	public function handle(Throwable $error): ApiResponse
	{
		// Rethrow error if it should not be catch (debug only)
		if (!$this->catchException) {

			// Unwrap exception from snapshot
			if ($error instanceof SnapshotException) {
				throw $error->getPrevious();
			}

			throw $error;
		}

		// Response is inside snapshot, return it
		if ($error instanceof SnapshotException) {
			return $error->getResponse();
		}

		// No response available, create new from error
		if (!$error instanceof ApiException) {
			$error = ServerErrorException::create()->withPrevious($error);
		}

		return $this->errorConverter->createResponseFromError($error);
	}

}
