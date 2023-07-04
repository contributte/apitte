<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class MediaType
{

	private Schema|Reference|null $schema = null;

	private mixed $example = null;

	/** @var string[]|Example[]|Reference[] */
	private array $examples = [];

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
		if (isset($data['examples'])) {
			foreach ($data['examples'] as $name => $example) {
				if (isset($example['$ref'])) {
					$mediaType->addExample($name, new Reference($example['$ref']));
				} else {
					$mediaType->addExample($name, Example::fromArray($example));
				}
			}
		}

		return $mediaType;
	}

	public function getSchema(): Schema|Reference|null
	{
		return $this->schema;
	}

	public function setSchema(Schema|Reference|null $schema): void
	{
		$this->schema = $schema;
	}

	public function getExample(): mixed
	{
		return $this->example;
	}

	public function setExample(mixed $example): void
	{
		$this->example = $example;
	}

	public function addExample(string $name, Example|Reference|string $example): void
	{
		$this->examples[$name] = $example;
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

		if ($this->examples !== []) {
			$data['examples'] = array_map(static fn ($example) => is_string($example) ? $example : $example->toArray(), $this->examples);
		}

		return $data;
	}

}
