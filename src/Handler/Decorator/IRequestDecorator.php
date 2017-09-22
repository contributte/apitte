<?php

namespace Apitte\Core\Handler\Decorator;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IRequestDecorator extends IDecorator
{

	/**
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface
	 */
	public function __invoke(ServerRequestInterface $request);

}
