<?php declare(strict_types = 1);

namespace Apitte\Negotiation;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ContentNegotiation
{

	// Attributes in ApiRequest
	public const ATTR_SKIP = 'apitte.negotiation.skip';

	/** @var INegotiator[] */
	protected $negotiators = [];

	/**
	 * @param INegotiator[] $negotiators
	 */
	public function __construct(array $negotiators = [])
	{
		$this->addNegotiations($negotiators);
	}

	/**
	 * @param INegotiator[] $negotiators
	 */
	public function addNegotiations(array $negotiators): void
	{
		foreach ($negotiators as $negotiator) {
			$this->addNegotiation($negotiator);
		}
	}

	public function addNegotiation(INegotiator $negotiator): void
	{
		$this->negotiators[] = $negotiator;
	}

	/**
	 * @param mixed[] $context
	 */
	public function negotiate(ApiRequest $request, ApiResponse $response, array $context = []): ApiResponse
	{
		// Should we skip negotiation?
		if ($request->getAttribute(self::ATTR_SKIP, false) === true) return $response;

		// Validation
		if ($this->negotiators === []) {
			throw new InvalidStateException('At least one response negotiator is required');
		}

		foreach ($this->negotiators as $negotiator) {
			// Pass to negotiator and check return value
			$negotiated = $negotiator->negotiate($request, $response, $context);

			// If it's not NULL, we have an ApiResponse
			if ($negotiated !== null) return $negotiated;
		}

		return $response;
	}

}
