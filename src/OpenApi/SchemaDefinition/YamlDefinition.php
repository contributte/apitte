<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

use Symfony\Component\Yaml\Yaml;

class YamlDefinition implements IDefinition
{

	private string $file;

	public function __construct(string $file)
	{
		$this->file = $file;
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		$decode = Yaml::parseFile($this->file);
		if ($decode === false || $decode === null) {
			return [];
		}

		return $decode;
	}

}
