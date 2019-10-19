<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class IdValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		$this->validateDuplicities($builder);
		$this->validateRegex($builder);
	}

	protected function validateDuplicities(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();
		$ids = [];

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {
				// Skip if @Id is not set
				if ($method->getId() === null || $method->getId() === '') {
					continue;
				}

				$fullid = implode('.', array_merge(
					$controller->getGroupIds(),
					[$controller->getId()],
					[$method->getId()]
				));

				// If this @GroupId(s).@ControllerId.@Id exists, throw an exception
				if (isset($ids[$fullid])) {
					throw new InvalidSchemaException(
						sprintf(
							'Duplicate @Id "%s" in "%s::%s()" and "%s::%s()"',
							$fullid,
							$controller->getClass(),
							$method->getName(),
							$ids[$fullid]['controller']->getClass(),
							$ids[$fullid]['method']->getName()
						)
					);
				}

				$ids[$fullid] = ['controller' => $controller, 'method' => $method];
			}
		}
	}

	protected function validateRegex(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {
				// Skip if @Id is not set
				if ($method->getId() === null || $method->getId() === '') {
					continue;
				}

				$id = $method->getId();

				// Allowed characters:
				// -> a-z
				// -> A-Z
				// -> 0-9
				// -> _
				$match = Regex::match($id, '#([^a-zA-Z0-9_]+)#');

				if ($match !== null) {
					throw new InvalidSchemaException(
						sprintf(
							'@Id "%s" in "%s::%s()" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9_].',
							$id,
							$controller->getClass(),
							$method->getName(),
							$match[1]
						)
					);
				}
			}
		}
	}

}
