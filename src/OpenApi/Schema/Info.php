<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Info
{

	private string $title;

	private ?string $description = null;

	private ?string $termsOfService = null;

	private ?Contact $contact = null;

	private ?License $license = null;

	private string $version;

	public function __construct(string $title, string $version)
	{
		$this->title = $title;
		$this->version = $version;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['title'] = $this->title;

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->termsOfService !== null) {
			$data['termsOfService'] = $this->termsOfService;
		}

		if ($this->contact !== null) {
			$data['contact'] = $this->contact->toArray();
		}

		if ($this->license !== null) {
			$data['license'] = $this->license->toArray();
		}

		$data['version'] = $this->version;

		return $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Info
	{
		$info = new Info($data['title'], $data['version']);
		$info->setDescription($data['description'] ?? null);
		$info->setTermsOfService($data['termsOfService'] ?? null);
		$info->setLicense(isset($data['license']) ? License::fromArray($data['license']) : null);
		$info->setContact(isset($data['contact']) ? Contact::fromArray($data['contact']) : null);
		return $info;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setTermsOfService(?string $termsOfService): void
	{
		$this->termsOfService = $termsOfService;
	}

	public function setContact(?Contact $contact): void
	{
		$this->contact = $contact;
	}

	public function setLicense(?License $license): void
	{
		$this->license = $license;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getTermsOfService(): ?string
	{
		return $this->termsOfService;
	}

	public function getContact(): ?Contact
	{
		return $this->contact;
	}

	public function getLicense(): ?License
	{
		return $this->license;
	}

	public function getVersion(): string
	{
		return $this->version;
	}

}
