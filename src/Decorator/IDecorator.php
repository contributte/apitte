<?php

namespace Apitte\Core\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IDecorator
{

	// Dispatcher
	const ON_DISPATCHER_EXCEPTION = 'dispatcher.exception';

	// Handler
	const ON_HANDLER_BEFORE = 'handler.before';
	const ON_HANDLER_AFTER = 'handler.after';

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $context
	 * @return ServerRequestInterface|ResponseInterface
	 */
	public function decorate(ServerRequestInterface $request, ResponseInterface $response, array $context = []);

}
