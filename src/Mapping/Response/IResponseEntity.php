<?php

namespace Apitte\Core\Mapping\Response;

interface IResponseEntity
{

	/**
	 * @return array
	 */
	public function getResponseProperties();

	/**
	 * @return array
	 */
	public function toResponse();

}
