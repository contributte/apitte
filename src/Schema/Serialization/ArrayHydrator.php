<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Apitte\Core\Schema\SchemaMapping;

final class ArrayHydrator implements IHydrator
{

	/**
	 * @param array $data
	 * @return Schema
	 */
	public function hydrate($data)
	{
		$schema = new Schema();

		foreach ($data as $route) {

			if (!isset($route[SchemaMapping::HANDLER])) {
				throw new InvalidStateException(sprintf('Structure %s is required', SchemaMapping::HANDLER));
			}

			$handler = new EndpointHandler();
			$handler->setClass($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_CLASS]);
			$handler->setMethod($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_METHOD]);
			$handler->setArguments($route[SchemaMapping::HANDLER][SchemaMapping::HANDLER_ARGUMENTS]);

			$endpoint = new Endpoint();
			$endpoint->setHandler($handler);
			$endpoint->setMethods($route[SchemaMapping::METHODS]);
			$endpoint->setMask($route[SchemaMapping::MASK]);
			$endpoint->setPattern($route[SchemaMapping::PATTERN]);

			if (isset($route[SchemaMapping::GROUP])) {
				if (isset($route[SchemaMapping::GROUP][SchemaMapping::GROUP_IDS])) {
					$endpoint->addTag(Endpoint::TAG_GROUP_IDS, (array) $route[SchemaMapping::GROUP][SchemaMapping::GROUP_IDS]);
				}
				if (isset($route[SchemaMapping::GROUP][SchemaMapping::GROUP_PATHS])) {
					$endpoint->addTag(Endpoint::TAG_GROUP_PATHS, (array) $route[SchemaMapping::GROUP][SchemaMapping::GROUP_PATHS]);
				}
			}

			if (isset($route[SchemaMapping::ID])) {
				$endpoint->addTag(Endpoint::TAG_ID, $route[SchemaMapping::ID]);
			}

			if (isset($route[SchemaMapping::TAGS])) {
				foreach ($route[SchemaMapping::TAGS] as $name => $value) {
					$endpoint->addTag($name, $value);
				}
			}

			foreach ($route[SchemaMapping::PARAMETERS] as $param) {
				$parameter = new EndpointParameter();
				$parameter->setName($param[SchemaMapping::PARAMETERS_NAME]);
				$parameter->setType($param[SchemaMapping::PARAMETERS_TYPE]);
				$parameter->setDescription($param[SchemaMapping::PARAMETERS_DESCRIPTION]);
				$endpoint->addParameter($parameter);
			}

			$schema->addEndpoint($endpoint);
		}

		return $schema;
	}

}
