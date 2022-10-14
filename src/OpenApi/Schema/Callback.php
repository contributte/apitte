<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Callback
{

	/** @var mixed[] */
	private array $data = [];

	/**
	 * @param mixed[] $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Callback
	{
		return new Callback($data);
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return $this->data;
	}

}
