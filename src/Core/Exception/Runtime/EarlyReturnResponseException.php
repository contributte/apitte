<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Apitte\Core\Http\ApiResponse;

class EarlyReturnResponseException extends RuntimeException
{

	protected ApiResponse $response;

	public function __construct(ApiResponse $response)
	{
		parent::__construct();

		$this->response = $response;
	}

	public function getResponse(): ApiResponse
	{
		return $this->response;
	}

}
