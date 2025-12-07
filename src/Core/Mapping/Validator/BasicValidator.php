<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Mapping\Request\BasicEntity;
use ReflectionObject;

class BasicValidator implements IEntityValidator
{

	/**
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate(object $entity): void
	{
		// Only BasicEntity implements required method for
		// handling properties, etc...
		if (!($entity instanceof BasicEntity)) {
			return;
		}

		$violations = $this->validateProperties($entity);

		if ($violations !== []) {
			$fields = [];

			foreach ($violations as $property => $messages) {
				$fields[$property] = count($messages) > 1 ? $messages : $messages[0];
			}

			throw ValidationException::create()
				->withFields($fields);
		}
	}

	/**
	 * @param BasicEntity<int|string, mixed> $entity
	 * @return string[][]
	 */
	protected function validateProperties(BasicEntity $entity): array
	{
		$violations = [];
		$properties = $entity->getProperties();
		$rf = new ReflectionObject($entity);

		foreach (array_keys($properties) as $propertyName) {
			$propertyRf = $rf->getProperty($propertyName);
			$doc = (string) $propertyRf->getDocComment();

			if (str_contains($doc, '@required')) {
				$wasAccessible = $propertyRf->isPublic();

				if (!$wasAccessible) {
					$propertyRf->setAccessible(true);
				}

				$value = $propertyRf->getValue($entity);

				if (!$wasAccessible) {
					$propertyRf->setAccessible(false);
				}

				if ($value === null) {
					$violations[$propertyName][] = 'This value should not be null.';
				}
			}
		}

		return $violations;
	}

}
