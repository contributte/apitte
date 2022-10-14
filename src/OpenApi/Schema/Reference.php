<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Reference
{

	/** @var string */
	private string $ref;

	public function __construct(string $ref)
	{
		$this->ref = $ref;
	}

	public function getRef(): string
	{
		return $this->ref;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return ['$ref' => $this->ref];
	}

}
