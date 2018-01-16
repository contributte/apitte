<?php

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Request\AbstractEntity;
use Apitte\Core\Schema\Endpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestEntityMapping
{

	/**
	 * MAPPING *****************************************************************
	 */

	/**
	 * @param ServerRequestInterface $request
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

		$entityClass = $requestMapper->getEntity();
		$entity = new $entityClass;

		// Validate entity type
		if (!($entity instanceof AbstractEntity)) {
			throw new InvalidStateException(sprintf('Instantiated entity "%s" is not subclass of "%s"', get_class($entity), AbstractEntity::class));
		}

		// Convert request to entity
		$entity = $entity->fromRequest($request);

		// Process entity
		$entity = $this->process($entity);

		if ($entity) {
			$request = $request->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, $entity);
		}

		return $request;
	}

	/**
	 * @param AbstractEntity $entity
	 * @return AbstractEntity
	 */
	protected function process(AbstractEntity $entity)
	{
		return $entity;
	}

}
