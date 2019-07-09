<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandling;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;

class JsonErrorConverter implements ErrorConverter
{

	public function createResponseFromError(ApiException $error, ?ApiRequest $request = null, ?ApiResponse $response = null): ApiResponse
	{
		$code = $error->getCode();

		$data = [
			'status' => 'error',
			'code' => $code,
			'message' => $error->getMessage(),
		];

		if (($context = $error->getContext()) !== null) {
			$data['context'] = $context;
		}

		$body = stream_for(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0)));

		if ($response === null) {
			$response = new ApiResponse(new Response());
		}

		return $response
			->withStatus($code)
			->withHeader('Content-Type', 'application/json')
			->withBody($body);
	}

}
