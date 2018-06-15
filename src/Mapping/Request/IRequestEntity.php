<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;

interface IRequestEntity
{

	/**
	 * @return mixed[]
	 */
	public function getRequestProperties(): array;

	public function fromRequest(ApiRequest $request): ?self;

}
