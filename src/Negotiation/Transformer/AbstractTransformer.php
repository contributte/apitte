<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Transformer;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Http\AbstractEntity;

abstract class AbstractTransformer implements ITransformer
{

	protected function getEntity(ApiResponse $response): AbstractEntity
	{
		$entity = $response->getEntity();
		if ($entity === null) {
			throw new InvalidStateException('Entity is required');
		}

		return $entity;
	}

}
