<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Apitte\Core\UI\Controller\IController;
use Apitte\Core\Utils\Helpers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceCallback
{

	public function __construct(
		private readonly IController $service,
		private readonly string $method,
	)
	{
	}

	public function getService(): IController
	{
		return $this->service;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): mixed
	{
		return call_user_func(Helpers::callback([$this->service, $this->method]), $request, $response);
	}

}
