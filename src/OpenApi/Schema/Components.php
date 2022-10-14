<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Components
{

	/** @var Schema[]|Reference[] */
	private array $schemas = [];

	/** @var Response[]|Reference[] */
	private array $responses = [];

	/** @var Parameter[]|Reference[] */
	private array $parameters = [];

	/** @var Example[]|Reference[] */
	private array $examples = [];

	/** @var RequestBody[]|Reference[] */
	private array $requestBodies = [];

	/** @var Header[]|Reference[] */
	private array $headers = [];

	/** @var SecurityScheme[]|Reference[] */
	private array $securitySchemes = [];

	/** @var Link[]|Reference[] */
	private array $links = [];

	/** @var Callback[]|Reference[] */
	private array $callbacks = [];

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Components
	{
		$components = new Components();
		if (isset($data['schemas'])) {
			foreach ($data['schemas'] as $schemaKey => $schemaData) {
				$components->setSchema($schemaKey, Schema::fromArray($schemaData));
			}
		}

		if (isset($data['responses'])) {
			foreach ($data['responses'] as $responseKey => $responseData) {
				$components->setResponse($responseKey, Response::fromArray($responseData));
			}
		}

		if (isset($data['parameters'])) {
			foreach ($data['parameters'] as $parameterKey => $parameterData) {
				$components->setParameter($parameterKey, Parameter::fromArray($parameterData));
			}
		}

		if (isset($data['examples'])) {
			foreach ($data['examples'] as $exampleKey => $exampleData) {
				$components->setExample($exampleKey, Example::fromArray($exampleData));
			}
		}

		if (isset($data['requestBodies'])) {
			foreach ($data['requestBodies'] as $requestBodyKey => $requestBodyData) {
				$components->setRequestBody($requestBodyKey, RequestBody::fromArray($requestBodyData));
			}
		}

		if (isset($data['headers'])) {
			foreach ($data['headers'] as $headerKey => $headerData) {
				$components->setHeader($headerKey, Header::fromArray($headerData));
			}
		}

		if (isset($data['securitySchemes'])) {
			foreach ($data['securitySchemes'] as $securitySchemeKey => $securitySchemeData) {
				$components->setSecurityScheme($securitySchemeKey, SecurityScheme::fromArray($securitySchemeData));
			}
		}

		return $components;
	}

	/**
	 * @param Schema|Reference $schema
	 */
	public function setSchema(string $name, $schema): void
	{
		$this->schemas[$name] = $schema;
	}

	/**
	 * @param Response|Reference $response
	 */
	public function setResponse(string $name, $response): void
	{
		$this->responses[$name] = $response;
	}

	/**
	 * @param Parameter|Reference $parameter
	 */
	public function setParameter(string $name, $parameter): void
	{
		$this->parameters[$name] = $parameter;
	}

	/**
	 * @param Example|Reference $example
	 */
	public function setExample(string $name, $example): void
	{
		$this->examples[$name] = $example;
	}

	/**
	 * @param RequestBody|Reference $requestBody
	 */
	public function setRequestBody(string $name, $requestBody): void
	{
		$this->requestBodies[$name] = $requestBody;
	}

	/**
	 * @param Header|Reference $header
	 */
	public function setHeader(string $name, $header): void
	{
		$this->headers[$name] = $header;
	}

	/**
	 * @param SecurityScheme|Reference $securityScheme
	 */
	public function setSecurityScheme(string $name, $securityScheme): void
	{
		$this->securitySchemes[$name] = $securityScheme;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		foreach ($this->schemas as $schemaKey => $schema) {
			$data['schemas'][$schemaKey] = $schema->toArray();
		}

		foreach ($this->responses as $responseKey => $response) {
			$data['responses'][$responseKey] = $response->toArray();
		}

		foreach ($this->parameters as $parameterKey => $parameter) {
			$data['parameters'][$parameterKey] = $parameter->toArray();
		}

		foreach ($this->examples as $exampleKey => $example) {
			$data['examples'][$exampleKey] = $example->toArray();
		}

		foreach ($this->requestBodies as $requestBodyKey => $requestBody) {
			$data['requestBodies'][$requestBodyKey] = $requestBody->toArray();
		}

		foreach ($this->headers as $headerKey => $header) {
			$data['headers'][$headerKey] = $header->toArray();
		}

		foreach ($this->securitySchemes as $securitySchemeKey => $securityScheme) {
			$data['securitySchemes'][$securitySchemeKey] = $securityScheme->toArray();
		}

		return $data;
	}

}
