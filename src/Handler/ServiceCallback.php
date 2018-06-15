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

	/** @var mixed[] */
	private $arguments;

	/**
	 * @param object $service
	 * @param mixed[] $args
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function __construct($service, string $method, array $args = [])
	{
		$this->service = $service;
		$this->method = $method;
		$this->arguments = $args;
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
	 * @return mixed[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @param mixed[] $args
	 */
	public function setArguments(array $args): void
	{
		$this->arguments = $args;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		return call_user_func_array([$this->service, $this->method], $this->arguments);
	}

}
