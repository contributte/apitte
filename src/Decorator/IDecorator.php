<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IDecorator
{

	// Dispatcher
	public const ON_DISPATCHER_EXCEPTION = 'dispatcher.exception';

	// Handler
	public const
		ON_HANDLER_BEFORE = 'handler.before',
		ON_HANDLER_AFTER = 'handler.after';

	/**
	 * @param mixed[] $context
	 * @return ServerRequestInterface|ResponseInterface
	 */
	public function decorate(ServerRequestInterface $request, ResponseInterface $response, array $context = []);

}
