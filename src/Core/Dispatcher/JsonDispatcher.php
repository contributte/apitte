<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;

class JsonDispatcher extends CoreDispatcher
{

	public function fallback(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->withStatus(404)
			->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(Json::encode(['error' => 'No matched route by given URL']));

		return $response;
	}

	protected function handle(ApiRequest $request, ApiResponse $response): ApiResponse
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
			throw new InvalidStateException(sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
		}

		if (!($response instanceof ApiResponse)) { //TODO - deprecation warning
			$response = new ApiResponse($response);
		}

		return $response;
	}

}
