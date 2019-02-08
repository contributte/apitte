<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * @method Throwable getPrevious()
 */
class SnapshotException extends RuntimeException
{

	/** @var ServerRequestInterface */
	protected $request;

	/** @var ResponseInterface */
	protected $response;

	public function __construct(Throwable $exception, ServerRequestInterface $request, ResponseInterface $response)
	{
		parent::__construct($exception->getMessage(), $exception->getCode(), $exception);
		$this->request = $request;
		$this->response = $response;
	}

	public function getRequest(): ServerRequestInterface
	{
		return $this->request;
	}

	public function getResponse(): ResponseInterface
	{
		return $this->response;
	}

}
