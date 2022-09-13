<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class SecurityScheme
{

	/** @var string */
	private $type;

	/** @var string|null */
	private $name;

	/** @var string|null */
	private $description;

	/** @var string|null */
	private $in;

	/** @var string|null */
	private $template;

	/** @var string|null */
	private $scheme;

	/** @var string|null */
	private $bearerFormat;

	/** @var mixed[] */
	private $flows;

	public function __construct(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['type'] = $this->type;
		if ($this->name !== null) {
			$data['name'] = $this->name;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->in !== null) {
			$data['in'] = $this->in;
		}

		if ($this->template !== null) {
			$data['template'] = $this->template;
		}

		if ($this->scheme !== null) {
			$data['scheme'] = $this->scheme;
		}

		if ($this->bearerFormat !== null) {
			$data['bearerFormat'] = $this->bearerFormat;
		}

		if (count($this->flows) > 0) {
			$data['flows'] = $this->flows;
		}

		return $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): SecurityScheme
	{
		$securityScheme = new SecurityScheme($data['type']);
		$securityScheme->setName($data['name'] ?? null);
		$securityScheme->setDescription($data['description'] ?? null);
		$securityScheme->setIn($data['in'] ?? null);
		$securityScheme->setTemplate($data['template'] ?? null);
		$securityScheme->setScheme($data['scheme'] ?? null);
		$securityScheme->setBearerFormat($data['bearerFormat'] ?? null);
		$securityScheme->setFlows($data['flows'] ?? []);
		return $securityScheme;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getIn(): ?string
	{
		return $this->in;
	}

	public function setIn(?string $in): void
	{
		$this->in = $in;
	}

	public function getTemplate(): ?string
	{
		return $this->template;
	}

	public function setTemplate(?string $template): void
	{
		$this->template = $template;
	}

	public function getScheme(): ?string
	{
		return $this->scheme;
	}

	public function setScheme(?string $scheme): void
	{
		$this->scheme = $scheme;
	}

	public function getBearerFormat(): ?string
	{
		return $this->bearerFormat;
	}

	public function setBearerFormat(?string $bearerFormat): void
	{
		$this->bearerFormat = $bearerFormat;
	}

	public function getFlows(): mixed
	{
		return $this->flows;
	}

	public function setFlows(mixed $flows): void
	{
		$this->flows = $flows;
	}

}
