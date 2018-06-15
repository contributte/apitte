<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;

class SymfonyValidator implements IEntityValidator
{

	/**
	 * @param object $entity
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate($entity): void
	{
		AnnotationRegistry::registerLoader('class_exists');
		AnnotationReader::addGlobalIgnoredName('mapping');
		$annotationReader = new CachedReader(new AnnotationReader(), new ArrayCache());

		$validator = Validation::createValidatorBuilder()
			->enableAnnotationMapping($annotationReader)
			->getValidator();

		/** @var ConstraintViolationInterface[] $violations */
		$violations = $validator->validate($entity);

		if (count($violations) > 0) {
			$fields = [];
			foreach ($violations as $violation) {
				$fields[$violation->getPropertyPath()][] = $violation->getMessage();
			}

			throw ValidationException::create()
				->withFields($fields);
		}
	}

}
