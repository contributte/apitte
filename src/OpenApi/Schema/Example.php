<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Example
{

	private ?string $summary = null;

	private ?string $description = null;

	/** @var mixed|null */
	private $value = null;

	private ?string $externalValue = null;

	/**
	 * @param mixed[] $data
	 * @return self
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

	/**
	 * @return string|null
	 */
	public function getSummary(): ?string
	{
		return $this->summary;
	}

	/**
	 * @param string|null $summary
	 */
	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return mixed|null
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed|null $value
	 */
	public function setValue($value): void
	{
		$this->value = $value;
	}

	/**
	 * @return string|null
	 */
	public function getExternalValue(): ?string
	{
		return $this->externalValue;
	}

	/**
	 * @param string|null $externalValue
	 */
	public function setExternalValue(?string $externalValue): void
	{
		$this->externalValue = $externalValue;
	}

}
