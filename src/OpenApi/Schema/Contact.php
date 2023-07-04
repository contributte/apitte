<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Contact
{

	private ?string $name = null;

	private ?string $url = null;

	private ?string $email = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Contact
	{
		$contact = new Contact();
		$contact->setName($data['name'] ?? null);
		$contact->setUrl($data['url'] ?? null);
		$contact->setEmail($data['email'] ?? null);

		return $contact;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		if ($this->name !== null) {
			$data['name'] = $this->name;
		}

		if ($this->url !== null) {
			$data['url'] = $this->url;
		}

		if ($this->email !== null) {
			$data['email'] = $this->email;
		}

		return $data;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function setUrl(?string $url): void
	{
		$this->url = $url;
	}

	public function setEmail(?string $email): void
	{
		$this->email = $email;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

}
