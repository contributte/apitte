<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointNegotiation;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\EndpointRequest;
use Apitte\Core\Schema\EndpointRequestMapper;
use Apitte\Core\Schema\EndpointResponse;
use Apitte\Core\Schema\Schema;

final class ArrayHydrator implements IHydrator
{

	/**
	 * @param mixed $data
	 */
	public function hydrate($data): Schema
	{
		if (!is_array($data)) {
			throw new InvalidArgumentException(sprintf('%s support only arrays hydration.', static::class));
		}

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
	private function hydrateEndpoint(array $data): Endpoint
	{
		if (!isset($data['handler'])) {
			throw new InvalidStateException("Schema route 'handler' is required");
		}

		$handler = new EndpointHandler(
			$data['handler']['class'],
			$data['handler']['method']
		);

		$endpoint = new Endpoint($handler);
		$endpoint->setMethods($data['methods']);
		$endpoint->setMask($data['mask']);

		if (isset($data['description'])) {
			$endpoint->setDescription($data['description']);
		}

		if (isset($data['tags'])) {
			foreach ($data['tags'] as $name => $value) {
				$endpoint->addTag($name, $value);
			}
		}

		if (isset($data['id'])) {
			$endpoint->addTag(Endpoint::TAG_ID, $data['id']);
		}

		if (isset($data['attributes']['pattern'])) {
			$endpoint->setAttribute('pattern', $data['attributes']['pattern']);
		}

		if (isset($data['parameters'])) {
			foreach ($data['parameters'] as $param) {
				$parameter = new EndpointParameter(
					$param['name'],
					$param['type']
				);
				$parameter->setDescription($param['description']);
				$parameter->setIn($param['in']);
				$parameter->setRequired($param['required']);
				$parameter->setDeprecated($param['deprecated']);
				$parameter->setAllowEmpty($param['allowEmpty']);

				$endpoint->addParameter($parameter);
			}
		}

		if (isset($data['request'])) {
			$request = new EndpointRequest();
			if (isset($data['request']['description'])) {
				$request->setDescription($data['request']['description']);
			}
			if (isset($data['request']['entity'])) {
				$request->setEntity($data['request']['entity']);
			}
			if (isset($data['request']['required'])) {
				$request->setRequired($data['request']['required']);
			}
			$endpoint->setRequest($request);
		}

		if (isset($data['responses'])) {
			foreach ($data['responses'] as $res) {
				$response = new EndpointResponse(
					$res['code'],
					$res['description']
				);
				if (isset($res['entity'])) {
					$response->setEntity($res['entity']);
				}
				$endpoint->addResponse($response);
			}
		}

		if (isset($data['openApi'])) {
			$endpoint->setOpenApi($data['openApi']);
		}

		if (isset($data['negotiations'])) {
			foreach ($data['negotiations'] as $nego) {
				$negotiation = new EndpointNegotiation($nego['suffix']);
				$negotiation->setDefault($nego['default']);
				$negotiation->setRenderer($nego['renderer']);

				$endpoint->addNegotiation($negotiation);
			}
		}

		if (isset($data['requestMapper'])) {
			$requestMapper = new EndpointRequestMapper(
				$data['requestMapper']['entity'],
				$data['requestMapper']['validation']
			);
			$endpoint->setRequestMapper($requestMapper);
		}

		return $endpoint;
	}

}
