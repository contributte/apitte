<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Apitte\Core\UI\Controller\IController;
use Apitte\Core\Utils\Helpers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceCallback
{

	private IController $service;

	private string $method;

	public function __construct(IController $service, string $method)
	{
		$this->service = $service;
		$this->method = $method;
	}

	public function getService(): IController
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
		return call_user_func(Helpers::callback([$this->service, $this->method]), $request, $response);
	}

}
