<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

use Apitte\Core\Exception\Logical\InvalidArgumentException;

class Parameter
{

	public const IN_COOKIE = 'cookie';
	public const IN_HEADER = 'header';
	public const IN_PATH = 'path';
	public const IN_QUERY = 'query';

	public const INS = [
		self::IN_COOKIE,
		self::IN_HEADER,
		self::IN_PATH,
		self::IN_QUERY,
	];

	private string $name;

	private string $in;

	private ?string $description = null;

	private bool $required = false;

	private bool $deprecated = false;

	private bool $allowEmptyValue = false;

	private Schema|Reference|null $schema = null;

	private mixed $example = null;

	private ?string $style = null;

	public function __construct(string $name, string $in)
	{
		if (!in_array($in, self::INS, true)) {
			throw new InvalidArgumentException(sprintf(
				'Invalid value "%s" for attribute "in" given. It must be one of "%s".',
				$in,
				implode(', ', self::INS)
			));
		}

		$this->name = $name;
		$this->in = $in;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Parameter
	{
		$parameter = new Parameter($data['name'], $data['in']);
		$parameter->setDescription($data['description'] ?? null);
		$parameter->setRequired($data['required'] ?? false);
		if (isset($data['schema'])) {
			if (isset($data['schema']['$ref'])) {
				$parameter->setSchema(new Reference($data['schema']['$ref']));
			} else {
				$parameter->setSchema(Schema::fromArray($data['schema']));
			}
		}

		$parameter->setExample($data['example'] ?? null);
		$parameter->setStyle($data['style'] ?? null);

		return $parameter;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setRequired(bool $required): void
	{
		$this->required = $required;
	}

	public function setDeprecated(bool $deprecated): void
	{
		$this->deprecated = $deprecated;
	}

	public function setAllowEmptyValue(bool $allowEmptyValue): void
	{
		$this->allowEmptyValue = $allowEmptyValue;
	}

	public function setSchema(Schema|Reference|null $schema): void
	{
		$this->schema = $schema;
	}

	public function setExample(mixed $example): void
	{
		$this->example = $example;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['name'] = $this->name;
		$data['in'] = $this->in;
		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->required) {
			$data['required'] = $this->required;
		}

		if ($this->schema !== null) {
			$data['schema'] = $this->schema->toArray();
		}

		if ($this->example !== null) {
			$data['example'] = $this->example;
		}

		if ($this->style !== null) {
			$data['style'] = $this->style;
		}

		return $data;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getIn(): string
	{
		return $this->in;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}

	public function isAllowEmptyValue(): bool
	{
		return $this->allowEmptyValue;
	}

	public function getSchema(): Reference|Schema|null
	{
		return $this->schema;
	}

	public function getExample(): mixed
	{
		return $this->example;
	}

	public function getStyle(): ?string
	{
		return $this->style;
	}

	public function setStyle(?string $style): void
	{
		$this->style = $style;
	}

}
