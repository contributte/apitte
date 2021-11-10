<?php declare(strict_types = 1);

namespace Apitte\Negotiation;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Transformer\ITransformer;

class SuffixNegotiator implements INegotiator
{

	/** @var ITransformer[] */
	private $transformers = [];

	/**
	 * @param ITransformer[] $transformers
	 */
	public function __construct(array $transformers)
	{
		$this->addTransformers($transformers);
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
		if ($endpoint === null) return null;

		// Get negotiations
		$negotiations = $endpoint->getNegotiations();

		// Try match by allowed negotiations
		foreach ($negotiations as $negotiation) {
			// Normalize suffix
			$suffix = sprintf('.%s', ltrim($negotiation->getSuffix(), '.'));

			// Try match by suffix
			if ($this->match($request->getUri()->getPath(), $suffix)) {
				$transformer = ltrim($suffix, '.');

				// If callback is defined -> process to callback transformer
				if ($negotiation->getRenderer() !== null) {
					$transformer = INegotiator::RENDERER;
					$context['renderer'] = $negotiation->getRenderer();
				}

				if (!isset($this->transformers[$transformer])) {
					throw new InvalidStateException(sprintf('Transformer "%s" not registered', $transformer));
				}

				return $this->transformers[$transformer]->transform($request, $response, $context);
			}
		}

		return null;
	}

	/**
	 * Match transformer for the suffix? (.json?)
	 */
	private function match(string $path, string $suffix): bool
	{
		return substr($path, -strlen($suffix)) === $suffix;
	}

}
