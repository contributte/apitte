<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Mapping\RequestParameterMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestParametersDecorator implements IRequestDecorator
{

	/** @var RequestParameterMapping */
	protected $mapping;

	public function __construct(RequestParameterMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	public function decorateRequest(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
	{
		return $this->mapping->map($request, $response);
	}

}
