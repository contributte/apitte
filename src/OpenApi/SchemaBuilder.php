<?php declare(strict_types = 1);

namespace Apitte\OpenApi;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\OpenApi\SchemaDefinition\BaseDefinition;
use Apitte\OpenApi\SchemaDefinition\IDefinition;
use Apitte\OpenApi\SchemaDefinition\IVersionAwareDefinition;
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
		return OpenApi::fromArray($this->loadDefinitions());
	}

	/**
	 * Merges all the registered definitions into a single array, resolving the document
	 * version in two passes so version-aware definitions can adjust the shape of the
	 * schemas they generate.
	 *
	 * @return mixed[]
	 */
	protected function loadDefinitions(): array
	{
		$loaded = [];
		$probe = [];

		// First pass: definitions that do not depend on the version, and the version they declare
		foreach ($this->definitions as $index => $definition) {
			if ($definition instanceof IVersionAwareDefinition) {
				continue;
			}

			$loaded[$index] = $definition->load();
			$probe = Helpers::merge($loaded[$index], $probe);
		}

		$version = is_string($probe['openapi'] ?? null)
			? $probe['openapi']
			: BaseDefinition::DEFAULT_VERSION;

		// Second pass: merge in registration order, so priorities stay untouched
		$data = [];

		foreach ($this->definitions as $index => $definition) {
			if ($definition instanceof IVersionAwareDefinition) {
				$definition->setVersion($version);
				$data = Helpers::merge($definition->load(), $data);
			} else {
				$data = Helpers::merge($loaded[$index], $data);
			}
		}

		if (array_key_exists('openapi', $data) && !is_string($data['openapi'])) {
			throw new InvalidStateException(sprintf(
				'OpenAPI version must be a string, %s given. Quote the value in your configuration, e.g. openapi: \'3.1.1\'.',
				get_debug_type($data['openapi'])
			));
		}

		return $data;
	}

}
