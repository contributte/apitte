<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class MediaType
{

	/** @var Schema|Reference|null */
	private $schema;

	/** @var mixed */
	private $example;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): MediaType
	{
		$mediaType = new MediaType();
		if (isset($data['schema'])) {
			if (isset($data['schema']['$ref'])) {
				$mediaType->setSchema(new Reference($data['schema']['$ref']));
			} else {
				$mediaType->setSchema(Schema::fromArray($data['schema']));
			}
		}

		$mediaType->setExample($data['example'] ?? null);
		return $mediaType;
	}

	/**
	 * @return Schema|Reference|null
	 */
	public function getSchema()
	{
		return $this->schema;
	}

	/**
	 * @param Schema|Reference|null $schema
	 */
	public function setSchema($schema): void
	{
		$this->schema = $schema;
	}

	/**
	 * @return mixed
	 */
	public function getExample()
	{
		return $this->example;
	}

	/**
	 * @param mixed $example
	 */
	public function setExample($example): void
	{
		$this->example = $example;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		if ($this->schema !== null) {
			$data['schema'] = $this->schema->toArray();
		}

		if ($this->example !== null) {
			$data['example'] = $this->example;
		}

		return $data;
	}

}
