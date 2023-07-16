<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Reference
{

	private string $ref;

	private ?string $summary = null;

	private ?string $description = null;

	public function __construct(string $ref)
	{
		$this->ref = $ref;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Reference
	{
		$reference = new Reference($data['$ref']);
		$reference->setSummary($data['summary'] ?? null);
		$reference->setDescription($data['description'] ?? null);

		return $reference;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getRef(): string
	{
		return $this->ref;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [
			'$ref' => $this->ref,
		];

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		return $data;
	}

}
