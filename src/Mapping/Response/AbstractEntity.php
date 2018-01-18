<?php

namespace Apitte\Core\Mapping\Response;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

abstract class AbstractEntity implements IResponseEntity, IteratorAggregate
{

	/**
	 * @return array
	 */
	abstract public function toArray();

	/**
	 * @return ArrayIterator|Traversable
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->toArray());
	}

}
