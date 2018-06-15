<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Schema;

interface IHydrator
{

	/**
	 * @param mixed $data
	 */
	public function hydrate($data): Schema;

}
