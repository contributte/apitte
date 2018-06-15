<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointNegotiation;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\EndpointRequestMapper;
use Apitte\Core\Schema\EndpointResponseMapper;
use Apitte\Core\Schema\Schema;

final class ArrayHydrator implements IHydrator
{

	/**
	 * @param mixed[] $data
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function hydrate($data): Schema
	{
		$schema = new Schema();

		foreach ($data as $endpoint) {
			$endpoint = $this->hydrateEndpoint($endpoint);
			$schema->addEndpoint($endpoint);
		}

		return $schema;
	}

	/**
	 * @param mixed[] $data
	 */
	protected function hydrateEndpoint(array $data): Endpoint
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

		if (isset($data['attributes']['pattern'])) {
			$endpoint->setAttribute('pattern', $data['attributes']['pattern']);
		}

		if (isset($data['parameters'])) {
			foreach ($data['parameters'] as $param) {
				$parameter = new EndpointParameter();
				$parameter->setName($param['name']);
				$parameter->setType($param['type']);
				$parameter->setDescription($param['description']);
				$parameter->setIn($param['in']);
				$parameter->setRequired((bool) $param['required']);
				$parameter->setDeprecated((bool) $param['deprecated']);
				$parameter->setAllowEmpty((bool) $param['allowEmpty']);

				$endpoint->addParameter($parameter);
			}
		}

		if (isset($data['negotiations'])) {
			foreach ($data['negotiations'] as $nego) {
				$negotiation = new EndpointNegotiation();
				$negotiation->setSuffix($nego['suffix']);
				$negotiation->setDefault($nego['default']);
				$negotiation->setRenderer($nego['renderer']);

				$endpoint->addNegotiation($negotiation);
			}
		}

		if (isset($data['requestMapper'])) {
			$requestMapper = new EndpointRequestMapper();
			$requestMapper->setEntity($data['requestMapper']['entity']);
			$requestMapper->setValidation($data['requestMapper']['validation']);
			$endpoint->setRequestMapper($requestMapper);
		}

		if (isset($data['responseMapper'])) {
			$responseMapper = new EndpointResponseMapper();
			$responseMapper->setEntity($data['requestMapper']['entity']);
			$endpoint->setResponseMapper($responseMapper);
		}

		return $endpoint;
	}

}
