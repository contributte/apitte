<?php declare(strict_types = 1);

namespace Apitte\Negotiation;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Transformer\ITransformer;

class FallbackNegotiator implements INegotiator
{

	protected ITransformer $transformer;

	public function __construct(ITransformer $transformer)
	{
		$this->transformer = $transformer;
	}

	/**
	 * @param mixed[] $context
	 */
	public function negotiate(ApiRequest $request, ApiResponse $response, array $context = []): ?ApiResponse
	{
		return $this->transformer->transform($request, $response, $context);
	}

}
