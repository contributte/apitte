<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Mapping\RequestEntityMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestEntityDecorator implements IRequestDecorator
{

	/** @var RequestEntityMapping */
	protected $mapping;

	public function __construct(RequestEntityMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	/**
	 * @param ApiRequest $request
	 */
	public function decorateRequest(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
	{
		return $this->mapping->map($request, $response);
	}

}
