<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyValidator implements IEntityValidator
{

	private ?ConstraintValidatorFactoryInterface $constraintValidatorFactory = null;

	private ?TranslatorInterface $translator = null;

	private ?string $translationDomain = null;

	/** @var list<string>|null */
	private ?array $groups = null;

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
	 * @param list<string> $groups
	 */
	public function setGroups(array $groups): void
	{
		$this->groups = $groups;
	}

	/**
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate(object $entity): void
	{
		$validatorBuilder = Validation::createValidatorBuilder();
		$validatorBuilder->enableAttributeMapping();

		if ($this->constraintValidatorFactory !== null) {
			$validatorBuilder->setConstraintValidatorFactory($this->constraintValidatorFactory);
		}

		if ($this->translator !== null) {
			$validatorBuilder->setTranslator($this->translator);
			$validatorBuilder->setTranslationDomain($this->translationDomain);
		}

		$validator = $validatorBuilder->getValidator();

		$violations = $validator->validate($entity, null, $this->groups);

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
