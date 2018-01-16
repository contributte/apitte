<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;

interface IRequestEntity
{

	/**
	 * @return array
	 */
	public function getProperties();

	/**
	 * @param ApiRequest $request
	 * @return static
	 */
	public function fromRequest(ApiRequest $request);

}
