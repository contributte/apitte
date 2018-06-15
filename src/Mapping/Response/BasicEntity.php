<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Response;

use Apitte\Core\Mapping\TReflectionProperties;

abstract class BasicEntity extends AbstractEntity
{

	use TReflectionProperties;

	/**
	 * @return mixed[]
	 */
	public function getResponseProperties(): array
	{
		return $this->getProperties();
	}

	/**
	 * @return mixed[]
	 */
	public function toResponse(): array
	{
		return $this->toArray();
	}

}
