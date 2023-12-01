<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class SymfonyValidator implements IEntityValidator
{

	private ?Reader $reader;

	private ?ConstraintValidatorFactoryInterface $constraintValidatorFactory = null;

	public function __construct(?Reader $reader = null)
	{
		$this->reader = $reader;
		AnnotationReader::addGlobalIgnoredName('mapping');
	}

	public function setConstraintValidatorFactory(ConstraintValidatorFactoryInterface $constraintValidatorFactory): void
	{
		$this->constraintValidatorFactory = $constraintValidatorFactory;
	}

	/**
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate(object $entity): void
	{
		// @phpstan-ignore-next-line
		$validatorBuilder = Validation::createValidatorBuilder();
		$validatorBuilder->enableAttributeMapping();

		if (method_exists($validatorBuilder, 'setDoctrineAnnotationReader')) {
			$validatorBuilder->setDoctrineAnnotationReader($this->reader);
		}

		if ($this->constraintValidatorFactory !== null) {
			$validatorBuilder->setConstraintValidatorFactory($this->constraintValidatorFactory);
		}

		$validator = $validatorBuilder->getValidator();

		/** @var ConstraintViolationListInterface $violations */
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
