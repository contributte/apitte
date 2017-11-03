<?php

namespace Apitte\Core\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IDecorator
{

	const DISPATCHER_BEFORE = 'dispatcher.before';
	const DISPATCHER_AFTER = 'dispatcher.after';
	const DISPATCHER_EXCEPTION = 'dispatcher.exception';

	const HANDLER_BEFORE = 'handler.before';

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $context
	 * @return ServerRequestInterface|ResponseInterface
	 */
	public function decorate(ServerRequestInterface $request, ResponseInterface $response, array $context = []);

}
