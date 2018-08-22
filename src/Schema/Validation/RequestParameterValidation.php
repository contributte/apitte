<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\EndpointParameter;

class RequestParameterValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		foreach ($builder->getControllers() as $controller) {
			foreach ($controller->getMethods() as $method) {

				foreach ($method->getParameters() as $parameter) {
					if (!in_array($parameter->getType(), EndpointParameter::TYPES, true)) {
						throw new InvalidSchemaException(sprintf(
							'Invalid request parameter "type=%s" given in "%s::%s()". Choose one of %s::TYPE_*',
							$parameter->getType(),
							$controller->getClass(),
							$method->getName(),
							EndpointParameter::class
						));
					}

					if (!in_array($parameter->getIn(), EndpointParameter::IN, true)) {
						throw new InvalidSchemaException(sprintf(
							'Invalid request parameter "in=%s" given in "%s::%s()". Choose one of %s::IN_*',
							$parameter->getIn(),
							$controller->getClass(),
							$method->getName(),
							EndpointParameter::class
						));
					}
				}

			}
		}
	}

}
