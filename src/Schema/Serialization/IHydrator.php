<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Schema;

interface IHydrator
{

	/**
	 * @param mixed $data
	 * @return Schema
	 */
	public function hydrate($data);

}
