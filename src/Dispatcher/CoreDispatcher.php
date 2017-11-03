<?php

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Exception\Logical\BadRequestException;
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

	/**
	 * @param IRouter $router
	 * @param IHandler $handler
	 */
	public function __construct(IRouter $router, IHandler $handler)
	{
		$this->router = $router;
		$this->handler = $handler;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
	{
		// Try match request to our routes
		$matchedRequest = $this->match($request, $response);

		// If there is no match route <=> endpoint,
		if ($matchedRequest === NULL) {
			return $this->fallback($request, $response);
		}

		// According to matched endpoint, forward to handler
		return $this->handle($matchedRequest, $response);
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ServerRequestInterface
	 */
	protected function match(ServerRequestInterface $request, ResponseInterface $response)
	{
		return $this->router->match($request);
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	protected function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		$response = $this->handler->handle($request, $response);

		// Validate if response is ResponseInterface
		if (!($response instanceof ResponseInterface)) {
			throw new InvalidStateException(sprintf('Handler returned response must implement "%s"', ResponseInterface::class));
		}

		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface|void
	 */
	protected function fallback(ServerRequestInterface $request, ResponseInterface $response)
	{
		throw new BadRequestException('No matched route by given URL', 404);
	}

}
