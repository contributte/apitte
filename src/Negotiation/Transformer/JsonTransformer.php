<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Transformer;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Utils\Json;

class JsonTransformer extends AbstractTransformer
{

	/**
	 * Encode given data for response
	 *
	 * @param mixed[] $context
	 */
	public function transform(ApiRequest $request, ApiResponse $response, array $context = []): ApiResponse
	{
		if (isset($context['exception'])) {
			$exception = $context['exception'];
			// Convert exception to json
			$content = Json::encode($this->extractException($exception));
			$response = $response->withStatus($exception->getCode());
		} else {
			// Convert data to array to json
			$content = Json::encode($this->extractData($request, $response, $context));
		}

		$response->getBody()->write($content);

		// Setup content type
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	/**
	 * @param mixed[] $context
	 */
	protected function extractData(ApiRequest $request, ApiResponse $response, array $context): mixed
	{
		return $this->getEntity($response)->getData();
	}

	/**
	 * @return mixed[]
	 */
	protected function extractException(ApiException $exception): array
	{
		$data = [
			'exception' => $exception->getMessage(),
		];

		$context = $exception->getContext();

		if ($context !== null) {
			$data['context'] = $context;
		}

		return $data;
	}

}
