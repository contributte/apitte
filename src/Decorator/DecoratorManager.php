<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DecoratorManager
{

	/** @var IDecorator[][] */
	protected $decorators = [];

	public function addDecorator(string $type, IDecorator $decorator): void
	{
		if (!isset($this->decorators[$type])) {
			$this->decorators[$type] = [];
		}

		$this->decorators[$type][] = $decorator;
	}

	/**
	 * @param mixed[] $context
	 */
	public function decorateRequest(string $type, ServerRequestInterface $request, ResponseInterface $response, array $context = []): ?ServerRequestInterface
	{
		$decorators = $this->decorators[$type] ?? [];

		/** @var IDecorator $decorator */
		foreach ($decorators as $decorator) {
			/** @var ServerRequestInterface|null $request */
			$request = $decorator->decorate($request, $response, $context);
		}

		return $request;
	}

	/**
	 * @param mixed[] $context
	 */
	public function decorateResponse(string $type, ServerRequestInterface $request, ResponseInterface $response, array $context = []): ?ResponseInterface
	{
		$decorators = $this->decorators[$type] ?? [];

		// If there is no exception handler defined so return null (and exception will be thrown in DecoratedDispatcher)
		if ($type === IDecorator::ON_DISPATCHER_EXCEPTION && $decorators === []) return null;

		/** @var IDecorator $decorator */
		foreach ($decorators as $decorator) {
			/** @var ResponseInterface|null $response */
			$response = $decorator->decorate($request, $response, $context);
		}

		return $response;
	}

	public function hasDecorators(string $type): bool
	{
		return isset($this->decorators[$type]);
	}

}
