<?php

namespace Apitte\Core\Handler\Decorator;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IResponseDecorator extends IDecorator
{

	/**
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function __invoke(ResponseInterface $response);

}
