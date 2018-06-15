<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointNegotiation
{

	/** @var string|null */
	private $suffix;

	/** @var bool */
	private $default = false;

	/** @var string|null */
	private $renderer;

	public function getSuffix(): ?string
	{
		return $this->suffix;
	}

	public function setSuffix(?string $suffix): void
	{
		$this->suffix = $suffix;
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

	public function setRenderer(string $renderer): void
	{
		$this->renderer = $renderer;
	}

}
