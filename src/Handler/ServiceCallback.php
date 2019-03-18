<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceCallback
{

	/** @var object */
	private $service;

	/** @var string */
	private $method;

	/**
	 * @param object $service
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function __construct($service, string $method)
	{
		$this->service = $service;
		$this->method = $method;
	}

	/**
	 * @return object
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getService()
	{
		return $this->service;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @return mixed
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
	{
		return call_user_func([$this->service, $this->method], $request, $response);
	}

}
