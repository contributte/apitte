<?php

namespace Apitte\Core\Router;

use Apitte\Core\Http\ApiRequest;

interface IRouter
{

	/**
	 * @param ApiRequest $request
	 * @return ApiRequest|NULL
	 */
	public function match(ApiRequest $request);

}
