<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Request;

use ArrayIterator;
use IteratorAggregate;

/**
 * @template TKey of string|int
 * @template TValue of mixed
 * @implements IteratorAggregate<TKey, TValue>
 */
abstract class AbstractEntity implements IRequestEntity, IteratorAggregate
{

	/**
	 * @return array<TKey, TValue>
	 */
	abstract public function toArray(): array;

	/**
	 * @return ArrayIterator<TKey, TValue>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->toArray());
	}

}
