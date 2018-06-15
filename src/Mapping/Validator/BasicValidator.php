<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Mapping\Request\BasicEntity;
use Nette\Utils\Strings;
use ReflectionObject;

class BasicValidator implements IEntityValidator
{

	/**
	 * @param object $entity
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate($entity): void
	{
		// Only BasicEntity implements required method for
		// handling properties, etc...
		if (!($entity instanceof BasicEntity)) return;

		$violations = $this->validateProperties($entity);

		if (count($violations) > 0) {
			$fields = [];
			foreach ($violations as $property => $messages) {
				$fields[$property] = count($messages) > 1 ? $messages : $messages[0];
			}

			throw ValidationException::create()
				->withFields($fields);
		}
	}

	/**
	 * @return string[][]
	 */
	protected function validateProperties(BasicEntity $entity): array
	{
		$violations = [];
		$properties = $entity->getProperties();
		$rf = new ReflectionObject($entity);

		foreach ($properties as $propertyName => $property) {
			$propertyRf = $rf->getProperty($propertyName);
			$doc = $propertyRf->getDocComment();

			if (Strings::contains($doc, '@required') && $entity->{$propertyName} === null) {
				$violations[$propertyName][] = 'This value should not be null.';
			}
		}

		return $violations;
	}

}
