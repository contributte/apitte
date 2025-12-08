<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\Utils\Json;

class JsonDefinition implements IDefinition
{

	public function __construct(
		private readonly string $file,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		$content = file_get_contents($this->file);

		if ($content === false) {
			throw new InvalidStateException('Cant read file ' . $this->file);
		}

		$decode = Json::decode($content, forceArrays: true);

		if ($decode === false || $decode === null) {
			return [];
		}

		return $decode;
	}

}
