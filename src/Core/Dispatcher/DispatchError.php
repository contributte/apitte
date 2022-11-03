<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Throwable;

class DispatchError
{

	protected Throwable $error;

	protected ApiRequest $request;

	public function __construct(Throwable $error, ApiRequest $request)
	{
		$this->error = $error;
		$this->request = $request;
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
