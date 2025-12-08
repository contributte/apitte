<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Throwable;

class DispatchError
{

	public function __construct(
		protected Throwable $error,
		protected ApiRequest $request,
	)
	{
	}

	public function getError(): Throwable
	{
		return $this->error;
	}

	public function getRequest(): ApiRequest
	{
		return $this->request;
	}

}
