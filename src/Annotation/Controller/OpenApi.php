<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class OpenApi
{

	/** @var mixed[] */
	private $data;

	/**
	 * @param mixed[] $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @return mixed[]
	 */
	public function getData(): array
	{
		return $this->data;
	}

}
