<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointNegotiation;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;

final class ArrayHydrator implements IHydrator
{

	/**
	 * @param array $data
	 * @return Schema
	 */
	public function hydrate($data)
	{
		$schema = new Schema();

		foreach ($data as $endpoint) {
			$endpoint = $this->hydrateEndpoint($endpoint);
			$schema->addEndpoint($endpoint);
		}

		return $schema;
	}

	/**
	 * @param array $data
	 * @return Endpoint
	 */
	protected function hydrateEndpoint(array $data)
	{
		if (!isset($data['handler'])) {
			throw new InvalidStateException("Schema route 'handler' is required");
		}

		$endpoint = new Endpoint();
		$endpoint->setMethods($data['methods']);
		$endpoint->setMask($data['mask']);

		if (isset($data['description'])) {
			$endpoint->setDescription($data['description']);
		}

		$handler = new EndpointHandler();
		$handler->setClass($data['handler']['class']);
		$handler->setMethod($data['handler']['method']);
		$handler->setArguments($data['handler']['arguments']);
		$endpoint->setHandler($handler);

		if (isset($data['id'])) {
			$endpoint->addTag(Endpoint::TAG_ID, $data['id']);
		}

		if (isset($data['tags'])) {
			foreach ($data['tags'] as $name => $value) {
				$endpoint->addTag($name, $value);
			}
		}

		if (isset($data['attributes'])) {
			if (isset($data['attributes']['pattern'])) {
				$endpoint->setAttribute('pattern', $data['attributes']['pattern']);
			}
		}

		if (isset($data['parameters'])) {
			foreach ($data['parameters'] as $param) {
				$parameter = new EndpointParameter();
				$parameter->setName($param['name']);
				$parameter->setType($param['type']);
				$parameter->setDescription($param['description']);
				$endpoint->addParameter($parameter);
			}
		}

		if (isset($data['negotiations'])) {
			foreach ($data['negotiations'] as $nego) {
				$negotiation = new EndpointNegotiation();
				$negotiation->setSuffix($nego['suffix']);
				$negotiation->setDefault($nego['default']);
				$negotiation->setCallback($nego['callback']);
				$endpoint->addNegotiation($negotiation);
			}
		}

		return $endpoint;
	}

}
