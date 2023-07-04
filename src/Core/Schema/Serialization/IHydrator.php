<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Schema;

interface IHydrator
{

	public function hydrate(mixed $data): Schema;

}
