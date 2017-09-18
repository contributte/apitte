<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\ApiSchema;

interface IHydrator
{

	/**
	 * @param mixed $data
	 * @return ApiSchema
	 */
	public function hydrate($data);

}
