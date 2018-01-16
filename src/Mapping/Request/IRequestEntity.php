<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;

interface IRequestEntity
{

	/**
	 * @param ApiRequest $request
	 * @return static
	 */
	public function fromRequest(ApiRequest $request);

}
