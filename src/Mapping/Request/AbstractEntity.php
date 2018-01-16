<?php

namespace Apitte\Core\Mapping\Request;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractEntity
{

	/**
	 * @param ServerRequestInterface $request
	 * @return static
	 */
	abstract public function fromRequest(ServerRequestInterface $request);

}
