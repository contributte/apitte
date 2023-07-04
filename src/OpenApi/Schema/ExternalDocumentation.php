<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class ExternalDocumentation
{

	private ?string $description = null;

	private string $url;

	public function __construct(string $url)
	{
		$this->url = $url;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): ExternalDocumentation
	{
		$externalDocumentation = new ExternalDocumentation($data['url']);
		$externalDocumentation->setDescription($data['description'] ?? null);

		return $externalDocumentation;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['url'] = $this->url;

		// Optional
		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

}
