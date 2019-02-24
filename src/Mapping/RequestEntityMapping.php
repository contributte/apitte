<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Request\IRequestEntity;
use Apitte\Core\Mapping\Validator\IEntityValidator;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointRequestMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestEntityMapping
{

	/** @var IEntityValidator|null */
	protected $validator;

	public function setValidator(?IEntityValidator $validator): void
	{
		$this->validator = $validator;
	}

	/**
	 * @param ApiRequest $request
	 */
	public function map(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// If there's no request mapper, then skip it
		if (!($requestMapper = $endpoint->getRequestMapper())) return $request;

		// Create entity
		$entity = $this->createEntity($requestMapper, $request);

		if ($entity !== null) {
			$request = $request->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, $entity);
		}

		return $request;
	}

	/**
	 * @param ApiRequest $request
	 * @return IRequestEntity|object|null
	 */
	protected function createEntity(EndpointRequestMapper $mapper, ServerRequestInterface $request)
	{
		$entityClass = $mapper->getEntity();
		$entity = new $entityClass();

		// Allow modify entity in extended class
		$entity = $this->modify($entity, $request);

		if ($entity === null) {
			return null;
		}

		// Try to validate entity only if its enabled
		if ($mapper->isValidation() === true) {
			$this->validate($entity);
		}

		return $entity;
	}

	/**
	 * @param IRequestEntity|object $entity
	 * @param ApiRequest $request
	 * @return IRequestEntity|object|null
	 */
	protected function modify($entity, ServerRequestInterface $request)
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
		if (!$this->validator) return;
		$this->validator->validate($entity);
	}

}
