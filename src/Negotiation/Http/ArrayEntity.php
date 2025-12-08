<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @template TKey of string|int
 * @template TValue of mixed
 * @implements IteratorAggregate<TKey, TValue>
 */
class ArrayEntity extends AbstractEntity implements IteratorAggregate, Countable
{

	/**
	 * @param array<TKey, TValue> $data
	 */
	public function __construct(array $data)
	{
		parent::__construct($data);
	}

	/**
	 * @param array<TKey, TValue> $data
	 * @return static<TKey, TValue>
	 */
	public static function from(array $data): static
	{
		return new static($data);
	}

	/**
	 * @return array<TKey, TValue>
	 */
	public function toArray(): array
	{
		return (array) $this->getData();
	}

	/**
	 * @return ArrayIterator<TKey, TValue>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->toArray());
	}

	public function count(): int
	{
		return count($this->toArray());
	}

}
