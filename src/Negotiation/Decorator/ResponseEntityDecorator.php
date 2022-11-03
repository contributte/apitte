<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\ContentNegotiation;

class ResponseEntityDecorator implements IResponseDecorator, IErrorDecorator
{

	private ContentNegotiation $negotiation;

	public function __construct(ContentNegotiation $negotiation)
	{
		$this->negotiation = $negotiation;
	}

	public function decorateError(ApiRequest $request, ApiResponse $response, ApiException $error): ApiResponse
	{
		return $this->negotiation->negotiate($request, $response, ['exception' => $error]);
	}

	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		// Cannot negotiate response without entity
		if ($response->getEntity() === null) {
			return $response;
		}

		return $this->negotiation->negotiate($request, $response);
	}

}
