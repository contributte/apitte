<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class ExternalDocumentation
{

	/** @var string|null */
	private ?string $description = null;

	/** @var string */
	private string $url;

	public function __construct(string $url)
	{
		$this->url = $url;
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

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): ExternalDocumentation
	{
		$externalDocumentation = new ExternalDocumentation($data['url']);
		$externalDocumentation->setDescription($data['description'] ?? null);
		return $externalDocumentation;
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
