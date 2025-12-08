<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Throwable;

/**
 * @method Throwable getPrevious()
 */
class SnapshotException extends RuntimeException
{

	public function __construct(
		Throwable $exception,
		protected ApiRequest $request,
		protected ApiResponse $response,
	)
	{
		parent::__construct($exception->getMessage(), is_string($exception->getCode()) ? -1 : $exception->getCode(), $exception);
	}

	public function getRequest(): ApiRequest
	{
		return $this->request;
	}

	public function getResponse(): ApiResponse
	{
		return $this->response;
	}

}
