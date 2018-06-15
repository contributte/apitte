<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsonDispatcher extends CoreDispatcher
{

	protected function handle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$result = $this->handler->handle($request, $response);

		// Convert array and scalar into JSON
		// Or just pass response
		if (is_array($result) || is_scalar($result)) {
			$response = $response->withStatus(200)
				->withHeader('Content-Type', 'application/json');
			$response->getBody()->write(Json::encode($result));
		} else {
			$response = $result;
		}

		// Validate if response is ResponseInterface
		if (!($response instanceof ResponseInterface)) {
			throw new InvalidStateException(sprintf('Handler returned response must implement "%s"', ResponseInterface::class));
		}

		return $response;
	}

	public function fallback(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$response = $response->withStatus(404)
			->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(Json::encode(['error' => 'No matched route by given URL']));

		return $response;
	}

}
