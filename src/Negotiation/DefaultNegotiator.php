<?php declare(strict_types = 1);

namespace Apitte\Negotiation;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Transformer\ITransformer;

class DefaultNegotiator implements INegotiator
{

	/** @var ITransformer[] */
	private array $transformers = [];

	/**
	 * @param ITransformer[] $transformers
	 */
	public function __construct(array $transformers)
	{
		$this->addTransformers($transformers);
	}

	/**
	 * @param mixed[] $context
	 */
	public function negotiate(ApiRequest $request, ApiResponse $response, array $context = []): ?ApiResponse
	{
		if ($this->transformers === []) {
			throw new InvalidStateException('Please add at least one transformer');
		}

		// Early return if there's no endpoint
		$endpoint = $response->getEndpoint();
		if ($endpoint === null)

		return null;

		// Get negotiations
		$negotiations = $endpoint->getNegotiations();

		// Try default
		foreach ($negotiations as $negotiation) {
			// Skip non default negotiations
			if (!$negotiation->isDefault())

			continue;

			// Normalize suffix for transformer
			$transformer = ltrim($negotiation->getSuffix(), '.');

			// If callback is defined -> process to callback transformer
			if ($negotiation->getRenderer() !== null) {
				$transformer = INegotiator::RENDERER;
				$context['renderer'] = $negotiation->getRenderer();
			}

			// Try default negotiation
			if (!isset($this->transformers[$transformer])) {
				throw new InvalidStateException(sprintf('Transformer "%s" not registered', $transformer));
			}

			// Transform (fallback) data to given format
			return $this->transformers[$transformer]->transform($request, $response, $context);
		}

		return null;
	}

	/**
	 * @param ITransformer[] $transformers
	 */
	private function addTransformers(array $transformers): void
	{
		foreach ($transformers as $suffix => $transformer) {
			$this->addTransformer($suffix, $transformer);
		}
	}

	private function addTransformer(string $suffix, ITransformer $transformer): void
	{
		$this->transformers[$suffix] = $transformer;
	}

}
