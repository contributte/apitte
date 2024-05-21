<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyValidator implements IEntityValidator
{

	private ?Reader $reader;

	private ?ConstraintValidatorFactoryInterface $constraintValidatorFactory = null;

	private ?TranslatorInterface $translator = null;

	private ?string $translationDomain = null;

	public function __construct(?Reader $reader = null)
	{
		$this->reader = $reader;
		AnnotationReader::addGlobalIgnoredName('mapping');
	}

	public function setConstraintValidatorFactory(ConstraintValidatorFactoryInterface $constraintValidatorFactory): void
	{
		$this->constraintValidatorFactory = $constraintValidatorFactory;
	}

	public function setTranslator(TranslatorInterface $translator): void
	{
		$this->translator = $translator;
	}

	public function setTranslationDomain(string $translationDomain): void
	{
		$this->translationDomain = $translationDomain;
	}

	/**
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate(object $entity): void
	{
		$validatorBuilder = Validation::createValidatorBuilder();
		$validatorBuilder->enableAttributeMapping();

		if (method_exists($validatorBuilder, 'setDoctrineAnnotationReader')) {
			$validatorBuilder->setDoctrineAnnotationReader($this->reader);
		}

		if ($this->constraintValidatorFactory !== null) {
			$validatorBuilder->setConstraintValidatorFactory($this->constraintValidatorFactory);
		}

		if ($this->translator !== null) {
			$validatorBuilder->setTranslator($this->translator);
			$validatorBuilder->setTranslationDomain($this->translationDomain);
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
