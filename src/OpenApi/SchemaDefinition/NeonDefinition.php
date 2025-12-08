<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\Neon\Neon;

class NeonDefinition implements IDefinition
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
		$input = file_get_contents($this->file);

		if ($input === false) {
			throw new InvalidStateException('Cant read file ' . $this->file);
		}

		$decode = Neon::decode($input);

		if ($decode === false || $decode === null) {
			return [];
		}

		return $decode;
	}

}
