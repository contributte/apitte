<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Header
{

	private ?string $description = null;

	private Schema|Reference|null $schema = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Header
	{
		$header = new Header();
		$header->setDescription($data['description'] ?? null);
		$header->setSchema(isset($data['schema']) ? Schema::fromArray($data['schema']) : null);

		return $header;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->schema !== null) {
			$data['schema'] = $this->schema->toArray();
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setSchema(Schema|Reference|null $schema): void
	{
		$this->schema = $schema;
	}

}
