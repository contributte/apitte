<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class License
{

	private string $name;

	private ?string $identifier = null;

	private ?string $url = null;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): License
	{
		$license = new License($data['name']);
		$license->setIdentifier($data['identifier'] ?? null);
		$license->setUrl($data['url'] ?? null);

		return $license;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['name'] = $this->name;

		// Optional SPDX identifier
		if ($this->identifier !== null) {
			$data['identifier'] = $this->identifier;
		}

		// Optional url
		if ($this->url !== null) {
			$data['url'] = $this->url;
		}

		return $data;
	}

	public function setIdentifier(?string $identifier): void
	{
		$this->identifier = $identifier;
	}

	public function setUrl(?string $url): void
	{
		$this->url = $url;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getIdentifier(): ?string
	{
		return $this->identifier;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

}
