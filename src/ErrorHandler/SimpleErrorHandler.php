<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandler;

use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiResponse;
use GuzzleHttp\Psr7\Response;
use Throwable;
use function GuzzleHttp\Psr7\stream_for;

class SimpleErrorHandler implements IErrorHandler
{

	/** @var bool */
	private $catchException = true;

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

		$body = stream_for(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0)));

		$response = new ApiResponse(new Response());
		return $response
			->withStatus($code)
			->withHeader('Content-Type', 'application/json')
			->withBody($body);
	}

}
