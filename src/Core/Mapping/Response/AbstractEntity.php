<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Response;

use ArrayIterator;
use IteratorAggregate;

abstract class AbstractEntity implements IResponseEntity, IteratorAggregate
{

	/**
	 * @return mixed[]
	 */
	abstract public function toArray(): array;

	/**
	 * @return ArrayIterator<int|string, mixed>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->toArray());
	}

}
