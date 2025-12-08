<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use ReflectionClass;

class NegotiationValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		foreach ($builder->getControllers() as $controller) {
			foreach ($controller->getMethods() as $method) {

				$haveDefault = null;
				$takenSuffixes = [];

				foreach ($method->getNegotiations() as $negotiation) {
					if ($negotiation->isDefault()) {
						if ($haveDefault !== null) {
							throw new InvalidSchemaException(sprintf(
								'Multiple negotiations with "default=true" given in "%s::%s()". Only one negotiation could be default.',
								$controller->getClass(),
								$method->getName()
							));
						}

						$haveDefault = $negotiation;
					}

					if (!isset($takenSuffixes[$negotiation->getSuffix()])) {
						$takenSuffixes[$negotiation->getSuffix()] = $negotiation;
					} else {
						throw new InvalidSchemaException(sprintf(
							'Multiple negotiations with "suffix=%s" given in "%s::%s()". Each negotiation must have unique suffix',
							$negotiation->getSuffix(),
							$controller->getClass(),
							$method->getName()
						));
					}

					$renderer = $negotiation->getRenderer();

					if ($renderer !== null) {
						if (!class_exists($renderer)) {
							throw new InvalidSchemaException(sprintf(
								'Negotiation renderer "%s" in "%s::%s()" does not exists',
								$renderer,
								$controller->getClass(),
								$method->getName()
							));
						}

						$reflection = new ReflectionClass($renderer);

						if (!$reflection->hasMethod('__invoke')) {
							throw new InvalidSchemaException(sprintf(
								'Negotiation renderer "%s" in "%s::%s()" does not implement __invoke(ApiRequest $request, ApiResponse $response, array $context): ApiResponse',
								$renderer,
								$controller->getClass(),
								$method->getName()
							));
						}
					}
				}
			}
		}
	}

}
