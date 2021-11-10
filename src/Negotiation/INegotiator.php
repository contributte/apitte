<?php declare(strict_types = 1);

namespace Apitte\Negotiation;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface INegotiator
{

	public const RENDERER = '#';

	/**
	 * @param mixed[] $context
	 */
	public function negotiate(ApiRequest $request, ApiResponse $response, array $context = []): ?ApiResponse;

}
