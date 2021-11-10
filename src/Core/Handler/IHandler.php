<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IHandler
{

	/**
	 * @return mixed
	 */
	public function handle(ApiRequest $request, ApiResponse $response);

}
