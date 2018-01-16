<?php

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Request\IRequestEntity;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointRequestMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestEntityMapping
{

	/**
	 * MAPPING *****************************************************************
	 */

	/**
	 * @param ServerRequestInterface|ApiRequest $request
	 * @param ResponseInterface $response
	 * @return ServerRequestInterface
	 */
	public function map(ServerRequestInterface $request, ResponseInterface $response)
	{
		/** @var Endpoint $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// If there's no request mapper, then skip it
		if (!($requestMapper = $endpoint->getRequestMapper())) return $request;

		// Create entity
		$entity = $this->createEntity($requestMapper, $request);

		if ($entity) {
			$request = $request->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, $entity);
		}

		return $request;
	}

	/**
	 * @param EndpointRequestMapper $mapper
	 * @param ServerRequestInterface|ApiRequest $request
	 * @return IRequestEntity|NULL
	 */
	protected function createEntity(EndpointRequestMapper $mapper, ServerRequestInterface $request)
	{
		$entityClass = $mapper->getEntity();
		$entity = new $entityClass;

		// Validate entity type
		if (!($entity instanceof IRequestEntity)) {
			throw new InvalidStateException(sprintf('Instantiated entity "%s" does not implement "%s"', get_class($entity), IRequestEntity::class));
		}

		// Allow modify entity in children
		$entity = $this->modify($entity, $request);

		return $entity;
	}

	/**
	 * @param IRequestEntity $entity
	 * @param ServerRequestInterface|ApiRequest $request
	 * @return IRequestEntity|NULL
	 */
	protected function modify(IRequestEntity $entity, ServerRequestInterface $request)
	{
		return $entity->fromRequest($request);
	}

}
