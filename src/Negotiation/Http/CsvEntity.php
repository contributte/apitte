<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

class CsvEntity extends AbstractEntity
{

	/** @var string[] */
	private array $header = [];

	/** @var string[] */
	private array $rows = [];

	/**
	 * @param string[] $header
	 * @return static
	 */
	public function withHeader(array $header)
	{
		$this->header = $header;
		$this->update();

		return $this;
	}

	/**
	 * @param string[] $rows
	 * @return static
	 */
	public function withRows(array $rows)
	{
		$this->rows = $rows;
		$this->update();

		return $this;
	}

	private function update(): void
	{
		$this->setData($this->header === [] ? $this->rows : array_merge([$this->header], $this->rows));
	}

}
