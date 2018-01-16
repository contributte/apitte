<?php

namespace Apitte\Core\Decorator;

use Apitte\Core\Mapping\RequestEntityMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestEntityDecorator implements IDecorator
{

	/** @var RequestEntityMapping */
	protected $mapping;

	/**
	 * @param RequestEntityMapping $mapping
	 */
	public function __construct(RequestEntityMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $context
	 * @return ResponseInterface|ServerRequestInterface
	 */
	public function decorate(ServerRequestInterface $request, ResponseInterface $response, array $context = [])
	{
		return $this->mapping->map($request, $response);
	}

}
