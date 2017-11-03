<?php

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SnapshotException extends RuntimeException
{

	/** @var ServerRequestInterface */
	protected $request;

	/** @var ResponseInterface */
	protected $response;

	/**
	 * @param Exception $exception
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 */
	public function __construct(Exception $exception, ServerRequestInterface $request, ResponseInterface $response)
	{
		parent::__construct(NULL, 0, $exception);
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * @return ServerRequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return ResponseInterface
	 */
	public function getResponse()
	{
		return $this->response;
	}

}
