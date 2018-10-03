<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Router\IRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreDispatcher implements IDispatcher
{

	/** @var IRouter */
	protected $router;

	/** @var IHandler */
	protected $handler;

	public function __construct(IRouter $router, IHandler $handler)
	{
		$this->router = $router;
		$this->handler = $handler;
	}

	public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		// Try match request to our routes
		$matchedRequest = $this->match($request, $response);

		// If there is no match route <=> endpoint,
		if ($matchedRequest === null) {
			return $this->fallback($request, $response);
		}

		// According to matched endpoint, forward to handler
		return $this->handle($matchedRequest, $response);
	}

	protected function match(ServerRequestInterface $request, ResponseInterface $response): ?ServerRequestInterface
	{
		return $this->router->match($request);
	}

	protected function handle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$response = $this->handler->handle($request, $response);

		// Validate if response is ResponseInterface
		if (!($response instanceof ResponseInterface)) {
			throw new InvalidStateException(sprintf('Handler returned response must implement "%s"', ResponseInterface::class));
		}

		return $response;
	}

	protected function fallback(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		throw new ClientErrorException('No matched route by given URL', 404);
	}

}
