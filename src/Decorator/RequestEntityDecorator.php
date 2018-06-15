<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Mapping\RequestEntityMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestEntityDecorator implements IDecorator
{

	/** @var RequestEntityMapping */
	protected $mapping;

	public function __construct(RequestEntityMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	/**
	 * @param ApiRequest $request
	 * @param mixed[] $context
	 */
	public function decorate(ServerRequestInterface $request, ResponseInterface $response, array $context = []): ServerRequestInterface
	{
		return $this->mapping->map($request, $response);
	}

}
