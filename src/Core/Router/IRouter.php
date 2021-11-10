<?php declare(strict_types = 1);

namespace Apitte\Core\Router;

use Apitte\Core\Http\ApiRequest;

interface IRouter
{

	public function match(ApiRequest $request): ?ApiRequest;

}
