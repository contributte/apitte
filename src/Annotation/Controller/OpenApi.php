<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class OpenApi
{

	/** @var string */
	private $data;

	/**
	 * @param mixed[] $data
	 */
	public function __construct(array $data)
	{
		$this->data = $this->purifyDocblock($data['value']);
	}

	public function getData(): string
	{
		return $this->data;
	}

	private function purifyDocblock(string $docblock): string
	{
		// Removes useless whitespace and * from start of every line
		return preg_replace('#\s*\*\/$|^\s*\*\s{0,1}|^\/\*{1,2}#m', '', $docblock);
	}

}
