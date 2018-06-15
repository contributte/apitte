<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class EarlyReturnResponseException extends RuntimeException
{

	/** @var ResponseInterface */
	protected $response;

	public function __construct(ResponseInterface $response)
	{
		parent::__construct();
		$this->response = $response;
	}

	public function getResponse(): ResponseInterface
	{
		return $this->response;
	}

}
