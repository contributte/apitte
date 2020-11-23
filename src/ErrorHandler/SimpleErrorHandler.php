<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiResponse;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Nette\Utils\Json;
use Throwable;

class SimpleErrorHandler implements IErrorHandler
{

	/** @var bool */
	private $catchException = true;

	public function setCatchException(bool $catchException): void
	{
		$this->catchException = $catchException;
	}

	public function handle(DispatchError $dispatchError): ApiResponse
	{
		$error = $dispatchError->getError();

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
		return $this->createResponseFromError($error);
	}

	protected function createResponseFromError(Throwable $error): ApiResponse
	{
		$code = $error instanceof ApiException ? $error->getCode() : 500;

		$data = [
			'status' => 'error',
			'code' => $code,
			'message' => $error instanceof ApiException ? $error->getMessage() : ServerErrorException::$defaultMessage,
		];

		if ($error instanceof ApiException && ($context = $error->getContext()) !== null) {
			$data['context'] = $context;
		}

		$body = Utils::streamFor(Json::encode($data));

		$response = new ApiResponse(new Response());
		return $response
			->withStatus($code)
			->withHeader('Content-Type', 'application/json')
			->withBody($body);
	}

}
