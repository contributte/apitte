<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Transformer;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\DI\Container;

class RendererTransformer extends AbstractTransformer
{

	protected Container $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Encode given data for response
	 *
	 * @param mixed[] $context
	 */
	public function transform(ApiRequest $request, ApiResponse $response, array $context = []): ApiResponse
	{
		// Return immediately if context hasn't defined renderer
		if (!isset($context['renderer'])) return $response;

		// Fetch service
		$service = $this->container->getByType($context['renderer'], false);

		if (!$service) {
			throw new InvalidStateException(sprintf('Renderer "%s" is not registered in container', $context['renderer']));
		}

		if (!is_callable($service)) {
			throw new InvalidStateException(sprintf('Renderer "%s" must implement __invoke() method', $context['renderer']));
		}

		return $service($request, $response, $context);
	}

}
