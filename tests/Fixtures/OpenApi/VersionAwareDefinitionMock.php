<?php declare(strict_types = 1);

namespace Tests\Fixtures\OpenApi;

use Apitte\OpenApi\SchemaDefinition\IVersionAwareDefinition;

final class VersionAwareDefinitionMock implements IVersionAwareDefinition
{

	public ?string $receivedVersion = null;

	/**
	 * @param mixed[] $data
	 */
	public function __construct(
		private readonly array $data = [],
	)
	{
	}

	public function setVersion(string $version): void
	{
		$this->receivedVersion = $version;
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		return $this->data;
	}

}
