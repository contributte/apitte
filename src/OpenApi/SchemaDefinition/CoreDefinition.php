<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\EndpointResponse;
use Apitte\Core\Schema\Schema as ApiSchema;
use Apitte\OpenApi\SchemaDefinition\Entity\IEntityAdapter;
use Apitte\OpenApi\Utils\Helpers;

class CoreDefinition implements IDefinition
{

	protected ApiSchema $schema;

	private IEntityAdapter $entityAdapter;

	public function __construct(ApiSchema $schema, IEntityAdapter $entityAdapter)
	{
		$this->schema = $schema;
		$this->entityAdapter = $entityAdapter;
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		$data = ['paths' => []];
		foreach ($this->getEndpoints() as $endpoint) {
			foreach ($endpoint->getMethods() as $method) {
				$data['paths'][(string) $endpoint->getMask()][strtolower($method)] = $this->createOperation($endpoint);
			}

			$data = Helpers::merge($endpoint->getOpenApi()['controller'] ?? [], $data);
		}

		return $data;
	}

	/**
	 * @return mixed[]
	 */
	protected function createOperation(Endpoint $endpoint): array
	{
		$operation = [];

		// Tags
		$tags = $this->getOperationTags($endpoint);
		if ($tags !== []) {
			$operation['tags'] = array_keys($tags);
		}

		// Parameters
		foreach ($endpoint->getParameters() as $endpointParam) {
			$operation['parameters'][] = $this->createParameter($endpointParam);
		}

		$requestBody = $endpoint->getRequestBody();
		if ($requestBody !== null) {
			$operation['requestBody'] = $this->createRequestBody($requestBody);
		}

		$operation['responses'] = $this->createResponses($endpoint);

		// TODO deprecated
		// $operation->setDeprecated(false);

		$operation = Helpers::merge($endpoint->getOpenApi()['method'] ?? [], $operation);

		return $operation;
	}

	/**
	 * @return mixed[]
	 */
	protected function createRequestBody(EndpointRequestBody $requestBody): array
	{
		$requestBodyData = ['content' => []];

		if ($requestBody->isRequired()) {
			$requestBodyData['required'] = true;
		}

		$description = $requestBody->getDescription();
		if ($description !== null) {
			$requestBodyData['description'] = $description;
		}

		$entity = $requestBody->getEntity();
		if ($entity !== null) {
			$requestBodyData['content'] = [
				// TODO resolve content types
				'application/json' =>
					[
						'schema' => $this->entityAdapter->getMetadata($entity),
					],
			];
		}

		return $requestBodyData;
	}

	/**
	 * @return mixed[]
	 */
	protected function createResponses(Endpoint $endpoint): array
	{
		$responses = [];
		foreach ($endpoint->getResponses() as $response) {
			$responses[$response->getCode()] = $this->createResponse($response);
		}

		return $responses;
	}

	/**
	 * @return mixed[]
	 */
	protected function createResponse(EndpointResponse $response): array
	{
		$responseData = [
			'description' => $response->getDescription(),
		];

		$entity = $response->getEntity();
		if ($entity !== null) {
			$responseData['content'] = [
				// TODO resolve content types
				'application/json' =>
					[
						'schema' => $this->entityAdapter->getMetadata($entity),
					],
			];
		}

		return $responseData;
	}

	/**
	 * @return mixed[]
	 */
	protected function createParameter(EndpointParameter $endpointParameter): array
	{
		$parameter = [
			'name' => $endpointParameter->getName(),
			'in' => $endpointParameter->getIn(),
		];

		$parameterDescription = $endpointParameter->getDescription();
		if ($parameterDescription !== null) {
			$parameter['description'] = $parameterDescription;
		}

		$parameter['required'] = $endpointParameter->isRequired();
		$parameter['schema'] = ['type' => $endpointParameter->getSchemaType()];

		// $param->setAllowEmptyValue($endpointParam->isAllowEmpty());
		// $param->setDeprecated($endpointParam->isDeprecated());
		// TODO types should be bool but now are strings
		// TODO schema

		return $parameter;
	}

	/**
	 * @return Endpoint[]
	 */
	protected function getEndpoints(): array
	{
		return $this->schema->getEndpoints();
	}

	/**
	 * @return string[]
	 */
	protected function getOperationTags(Endpoint $endpoint): array
	{
		$tags = $endpoint->getTags();
		unset($tags[Endpoint::TAG_ID]);

		return $tags;
	}

}
