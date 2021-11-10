<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class License
{

	/** @var string */
	private $name;

	/** @var string|null */
	private $url;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['name'] = $this->name;

		// Optional url
		if ($this->url !== null) {
			$data['url'] = $this->url;
		}

		return $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): License
	{
		$license = new License($data['name']);
		$license->setUrl($data['url'] ?? null);
		return $license;
	}

	public function setUrl(?string $url): void
	{
		$this->url = $url;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

}
