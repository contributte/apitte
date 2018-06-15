<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

class NullValidator implements IEntityValidator
{

	/**
	 * @param object $entity
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate($entity): void
	{
		// Hell nothing..
	}

}
