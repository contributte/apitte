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
				$components->setResponse((string) $responseKey, Response::fromArray($responseData));
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

		foreach ($data['requestBodies'] ?? [] as $requestBodyKey => $requestBodyData) {
			$components->setRequestBody($requestBodyKey, RequestBody::fromArray($requestBodyData));
		}

		foreach ($data['headers'] ?? [] as $headerKey => $headerData) {
			$components->setHeader($headerKey, Header::fromArray($headerData));
		}

		foreach ($data['securitySchemes'] ?? [] as $securitySchemeKey => $securitySchemeData) {
			$components->setSecurityScheme($securitySchemeKey, SecurityScheme::fromArray($securitySchemeData));
		}

		foreach ($data['links'] ?? [] as $linkKey => $linkData) {
			$components->setLink($linkKey, Link::fromArray($linkData));
		}

		return $components;
	}

	public function setSchema(string $name, Schema|Reference $schema): void
	{
		$this->schemas[$name] = $schema;
	}

	public function setResponse(string $name, Response|Reference $response): void
	{
		$this->responses[$name] = $response;
	}

	public function setParameter(string $name, Parameter|Reference $parameter): void
	{
		$this->parameters[$name] = $parameter;
	}

	public function setExample(string $name, Example|Reference $example): void
	{
		$this->examples[$name] = $example;
	}

	public function setRequestBody(string $name, RequestBody|Reference $requestBody): void
	{
		$this->requestBodies[$name] = $requestBody;
	}

	public function setHeader(string $name, Header|Reference $header): void
	{
		$this->headers[$name] = $header;
	}

	public function setSecurityScheme(string $name, SecurityScheme|Reference $securityScheme): void
	{
		$this->securitySchemes[$name] = $securityScheme;
	}

	public function setLink(string $name, Link|Reference $link): void
	{
		$this->links[$name] = $link;
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

		foreach ($this->links as $linkKey => $link) {
			$data['links'][$linkKey] = $link->toArray();
		}

		return $data;
	}

}
