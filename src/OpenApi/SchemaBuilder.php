<?php declare(strict_types = 1);

namespace Apitte\OpenApi;

use Apitte\OpenApi\SchemaDefinition\IDefinition;
use Contributte\OpenApi\Schema\OpenApi;
use Contributte\OpenApi\Utils\Helpers;

class SchemaBuilder implements ISchemaBuilder
{

	/** @var IDefinition[] */
	private array $definitions = [];

	public function addDefinition(IDefinition $definition): void
	{
		$this->definitions[] = $definition;
	}

	public function build(): OpenApi
	{
		$data = $this->loadDefinitions();

		return OpenApi::fromArray($data);
	}

	/**
	 * @return mixed[]
	 */
	protected function loadDefinitions(): array
	{
		$data = [];
		foreach ($this->definitions as $definition) {
			$data = Helpers::merge($definition->load(), $data);
		}

		return $data;
	}

}
