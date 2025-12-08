<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Response;

use Apitte\Core\Mapping\TReflectionProperties;

/**
 * @template TKey of string|int
 * @template TValue of mixed
 * @extends AbstractEntity<TKey, TValue>
 */
abstract class BasicEntity extends AbstractEntity
{

	use TReflectionProperties;

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public function getResponseProperties(): array
	{
		return $this->getProperties();
	}

	/**
	 * @return array<TKey, TValue>
	 */
	public function toResponse(): array
	{
		return $this->toArray();
	}

}
