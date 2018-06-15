<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Response;

interface IResponseEntity
{

	/**
	 * @return mixed[]
	 */
	public function getResponseProperties(): array;

	/**
	 * @return mixed[]
	 */
	public function toResponse(): array;

}
