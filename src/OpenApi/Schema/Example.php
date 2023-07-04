<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Example
{

	private ?string $summary = null;

	private ?string $description = null;

	private mixed $value = null;

	private ?string $externalValue = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): self
	{
		$example = new Example();
		$example->summary = $data['summary'] ?? null;
		$example->description = $data['description'] ?? null;
		$example->value = $data['value'] ?? null;
		$example->externalValue = $data['externalValue'] ?? null;

		return $example;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->value !== null) {
			$data['value'] = $this->value;
		}

		if ($this->externalValue !== null) {
			$data['externalValue'] = $this->externalValue;
		}

		return $data;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getValue(): mixed
	{
		return $this->value;
	}

	public function setValue(mixed $value): void
	{
		$this->value = $value;
	}

	public function getExternalValue(): ?string
	{
		return $this->externalValue;
	}

	public function setExternalValue(?string $externalValue): void
	{
		$this->externalValue = $externalValue;
	}

}
