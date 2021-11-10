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

	/** @var ApiRequest */
	protected $request;

	/** @var ApiResponse */
	protected $response;

	public function __construct(Throwable $exception, ApiRequest $request, ApiResponse $response)
	{
		parent::__construct($exception->getMessage(), is_string($exception->getCode()) ? -1 : $exception->getCode(), $exception);
		$this->request = $request;
		$this->response = $response;
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
