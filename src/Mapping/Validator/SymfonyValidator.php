<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;

class SymfonyValidator implements IEntityValidator
{

	/** @var Reader */
	private $reader;

	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
		AnnotationReader::addGlobalIgnoredName('mapping');
	}

	/**
	 * @param object $entity
	 * @throws ValidationException
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate($entity): void
	{
		$validator = Validation::createValidatorBuilder()
			->enableAnnotationMapping($this->reader)
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
