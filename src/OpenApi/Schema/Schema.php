<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Schema
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
	public static function fromArray(array $data): Schema
	{
		return new Schema($data);
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return $this->data;
	}

}
