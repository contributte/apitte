<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

class BaseDefinition implements IDefinition
{

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		return [
			'openapi' => '3.0.2',
			'info' => [
				'title' => 'OpenAPI',
				'version' => '1.0.0',
			],
			'paths' => [],
		];
	}

}
