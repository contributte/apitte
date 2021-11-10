<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @template TKey of string|int
 * @implements IteratorAggregate<TKey, mixed>
 */
class ArrayEntity extends AbstractEntity implements IteratorAggregate, Countable
{

	/**
	 * @param mixed[] $data
	 */
	public function __construct(array $data)
	{
		parent::__construct($data);
	}

	/**
	 * @param mixed[] $data
	 * @return static
	 */
	public static function from(array $data): self
	{
		return new static($data);
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return (array) $this->getData();
	}

	/**
	 * @return ArrayIterator<TKey, mixed>
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
