<?php

namespace Apitte\Core\Handler;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DecoratedServiceHandler extends ServiceHandler
{

	/** @var IDecorator[] */
	protected $decorators = [];

	/**
	 * @param IDecorator $decorator
	 * @return void
	 */
	public function addDecorator(IDecorator $decorator)
	{
		$this->decorators[] = $decorator;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		foreach ($this->decorators as $decorator) {
			$response = $decorator($request, $response);

			// Validate if response is returned
			if (!$response) {
				throw new InvalidStateException(sprintf('Decorator "%s" must return response', get_class($decorator)));
			}

			// Validate if response is ApiResponse
			if (!($response instanceof ServerRequestInterface)) {
				throw new InvalidStateException(sprintf('Decorator returned response must be subtype of %s', ServerRequestInterface::class));
			}
		}

		return parent::handle($request, $response);
	}

}
