<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

class BaseDefinition implements IDefinition
{

	/**
	 * Version assumed when none is configured. Kept for backward compatibility.
	 */
	public const DEFAULT_VERSION = '3.0.2';

	public function __construct(
		private readonly string $version = self::DEFAULT_VERSION,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		return [
			'openapi' => $this->version,
			'info' => [
				'title' => 'OpenAPI',
				'version' => '1.0.0',
			],
			'paths' => [],
		];
	}

}
