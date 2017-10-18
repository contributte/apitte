<?php

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class EarlyReturnResponseException extends RuntimeException
{

	/** @var ResponseInterface */
	protected $response;

	/**
	 * @param ResponseInterface $response
	 */
	public function __construct(ResponseInterface $response)
	{
		parent::__construct();
		$this->response = $response;
	}

	/**
	 * @return ResponseInterface
	 */
	public function getResponse()
	{
		return $this->response;
	}

}
