<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Apitte\Core\Http\ApiResponse;

class EarlyReturnResponseException extends RuntimeException
{

	public function __construct(
		protected ApiResponse $response,
	)
	{
		parent::__construct();
	}

	public function getResponse(): ApiResponse
	{
		return $this->response;
	}

}
