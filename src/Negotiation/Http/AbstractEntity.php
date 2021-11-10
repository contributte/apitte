<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

abstract class AbstractEntity
{

	/** @var mixed */
	protected $data;

	/**
	 * @param mixed $data
	 */
	public function __construct($data = null)
	{
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	protected function setData($data): void
	{
		$this->data = $data;
	}

}
