<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class RequestBody
{

	private ?string $description = null;

	/** @var MediaType[] */
	private array $content = [];

	private bool $required = false;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): RequestBody
	{
		$requestBody = new RequestBody();
		$requestBody->setRequired($data['required'] ?? false);
		$requestBody->setDescription($data['description'] ?? null);
		foreach ($data['content'] ?? [] as $key => $mediaType) {
			$requestBody->addMediaType($key, MediaType::fromArray($mediaType));
		}

		return $requestBody;
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

		$data['content'] = [];
		foreach ($this->content as $key => $mediaType) {
			$data['content'][$key] = $mediaType->toArray();
		}

		if ($this->required) {
			$data['required'] = true;
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setRequired(bool $required): void
	{
		$this->required = $required;
	}

	public function addMediaType(string $key, MediaType $mediaType): void
	{
		$this->content[$key] = $mediaType;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return MediaType[]
	 */
	public function getContent(): array
	{
		return $this->content;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

}
