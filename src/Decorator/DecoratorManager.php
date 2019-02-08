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

		foreach ($decorators as $decorator) {
			/** @var ServerRequestInterface|null $request */
			$request = $decorator->decorate($request, $response, $context);

			if ($request === null) return null; // Cannot pass null to next decorator
		}

		return $request;
	}

	/**
	 * @param mixed[] $context
	 */
	public function decorateResponse(string $type, ServerRequestInterface $request, ResponseInterface $response, array $context = []): ?ResponseInterface
	{
		$decorators = $this->decorators[$type] ?? [];

		foreach ($decorators as $decorator) {
			/** @var ResponseInterface|null $response */
			$response = $decorator->decorate($request, $response, $context);

			if ($response === null) return null; // Cannot pass null to next decorator
		}

		return $response;
	}

	public function hasDecorators(string $type): bool
	{
		return isset($this->decorators[$type]);
	}

}
