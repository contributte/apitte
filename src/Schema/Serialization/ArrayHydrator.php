<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\ApiSchema;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\SchemaMapping;

final class ArrayHydrator implements IHydrator
{

	/**
	 * @param array $data
	 * @return ApiSchema
	 */
	public function hydrate($data)
	{
		$schema = new ApiSchema();

		foreach ($data as $route) {
			$handler = new EndpointHandler();
			$handler->setClass($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_CLASS]);
			$handler->setMethod($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_METHOD]);
			$handler->setArguments($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_ARGUMENTS]);

			$endpoint = new Endpoint();
			$endpoint->setHandler($handler);
			$endpoint->setMethods($route[SchemaMapping::METHODS]);
			$endpoint->setMask($route[SchemaMapping::MASK]);
			$endpoint->setPattern($route[SchemaMapping::PATTERN]);

			if (isset($route[SchemaMapping::GROUPS])) {
				$endpoint->addTag(Endpoint::TAG_GROUP, (array) $route[SchemaMapping::GROUPS]);
			}

			if (isset($route[SchemaMapping::TAGS])) {
				foreach ($route[SchemaMapping::TAGS] as $name => $value) {
					$endpoint->addTag($name, $value);
				}
			}

			foreach ($route[SchemaMapping::PARAMETERS] as $p) {
				$param = new EndpointParameter();
				$param->setName($p[SchemaMapping::PARAMETERS_NAME]);
				$param->setType($p[SchemaMapping::PARAMETERS_TYPE]);
				$param->setDescription($p[SchemaMapping::PARAMETERS_DESCRIPTION]);
				$endpoint->addParameter($param);
			}

			$schema->addEndpoint($endpoint);
		}

		return $schema;
	}

}
