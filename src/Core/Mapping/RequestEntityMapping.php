<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Request\IRequestEntity;
use Apitte\Core\Mapping\Validator\IEntityValidator;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointRequestBody;

class RequestEntityMapping
{

	/** @var IEntityValidator|null */
	protected $validator;

	public function setValidator(?IEntityValidator $validator): void
	{
		$this->validator = $validator;
	}

	public function map(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// If there's no request mapper, then skip it
		if (!($requestBody = $endpoint->getRequestBody())) {
			return $request;
		}

		// Create entity
		$entity = $this->createEntity($requestBody, $request);

		if ($entity !== null) {
			$request = $request->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, $entity);
		}

		return $request;
	}

	/**
	 * @return IRequestEntity|object|null
	 */
	protected function createEntity(EndpointRequestBody $requestBody, ApiRequest $request)
	{
		$entityClass = $requestBody->getEntity();

		if ($entityClass === null) {
			return null;
		}

		$entity = new $entityClass();

		// Allow modify entity in extended class
		$entity = $this->modify($entity, $request);

		if ($entity === null) {
			return null;
		}

		// Try to validate entity only if its enabled
		if ($requestBody->isValidation() === true) {
			$this->validate($entity);
		}

		return $entity;
	}

	/**
	 * @param IRequestEntity|object $entity
	 * @return IRequestEntity|object|null
	 */
	protected function modify($entity, ApiRequest $request)
	{
		if ($entity instanceof IRequestEntity) {
			return $entity->fromRequest($request);
		}

		return $entity;
	}

	/**
	 * @param object $entity
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	protected function validate($entity): void
	{
		if (!$this->validator) {
			return;
		}

		$this->validator->validate($entity);
	}

}
