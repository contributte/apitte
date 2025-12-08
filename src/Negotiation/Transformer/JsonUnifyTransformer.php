<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Transformer;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\ResponseAttributes;
use Apitte\Negotiation\Http\ArrayEntity;
use Nette\Utils\Json;

class JsonUnifyTransformer extends AbstractTransformer
{

	// Statuses
	public const STATUS_SUCCESS = 'success';
	public const STATUS_ERROR = 'error';

	/**
	 * Encode given data for response
	 *
	 * @param mixed[] $context
	 */
	public function transform(ApiRequest $request, ApiResponse $response, array $context = []): ApiResponse
	{
		$response = isset($context['exception']) ? $this->transformException($context['exception'], $request, $response) : $this->transformResponse($request, $response);

		// Convert data to array to json
		$content = Json::encode($this->getEntity($response)->getData());
		$response->getBody()->write($content);

		// Setup content type
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	protected function transformException(ApiException $exception, ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$entityData = [
			'status' => self::STATUS_ERROR,
		];

		if ($exception instanceof ClientErrorException) {
			$entityData['data'] = [
				'code' => $exception->getCode(),
				'error' => $exception->getMessage(),
			];

			$context = $exception->getContext();

			if ($context !== null) {
				$entityData['data']['context'] = $context;
			}
		} else {
			$entityData['message'] = $exception->getMessage();
		}

		return $response
			->withStatus($exception->getCode())
			->withAttribute(ResponseAttributes::ATTR_ENTITY, ArrayEntity::from($entityData));
	}

	protected function transformResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response
			->withAttribute(ResponseAttributes::ATTR_ENTITY, ArrayEntity::from([
				'status' => self::STATUS_SUCCESS,
				'data' => $this->getEntity($response)->getData(),
			]));
	}

}
