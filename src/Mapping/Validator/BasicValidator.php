<?php

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Mapping\Request\AbstractEntity;
use Nette\Utils\Strings;
use ReflectionObject;

class BasicValidator implements IEntityValidator
{

	/**
	 * @param object $entity
	 * @throws ValidationException
	 * @return void
	 */
	public function validate($entity)
	{
		// Only AbstractEntity implements required method for
		// handling properties, etc...
		if (!($entity instanceof AbstractEntity)) return;

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
	 * @param AbstractEntity $entity
	 * @return array
	 */
	protected function validateProperties(AbstractEntity $entity)
	{
		$violations = [];
		$properties = $entity->getProperties();
		$rf = new ReflectionObject($entity);

		foreach ($properties as $propertyName => $property) {
			$propertyRf = $rf->getProperty($propertyName);
			$doc = $propertyRf->getDocComment();

			if (Strings::contains($doc, '@required')) {
				if ($entity->{$propertyName} === NULL) {
					$violations[$propertyName][] = 'This value should not be null.';
				}
			}
		}

		return $violations;
	}

}
