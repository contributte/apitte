<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointNegotiation
{

	private string $suffix;

	private bool $default = false;

	private ?string $renderer = null;

	public function __construct(string $suffix)
	{
		$this->suffix = $suffix;
	}

	public function getSuffix(): string
	{
		return $this->suffix;
	}

	public function isDefault(): bool
	{
		return $this->default;
	}

	public function setDefault(bool $default): void
	{
		$this->default = $default;
	}

	public function getRenderer(): ?string
	{
		return $this->renderer;
	}

	public function setRenderer(?string $renderer): void
	{
		$this->renderer = $renderer;
	}

}
