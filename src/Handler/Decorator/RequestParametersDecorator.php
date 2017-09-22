<?php

namespace Apitte\Core\Handler\Decorator;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class RequestParameterDecorator implements IRequestDecorator
{

	/**
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface
	 */
	public function decorateRequest(ServerRequestInterface $request)
	{
		$stop();
		// TODO: Implement decorateRequest() method.
	}

}
