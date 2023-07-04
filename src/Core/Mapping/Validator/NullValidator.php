<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Validator;

class NullValidator implements IEntityValidator
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function validate(object $entity): void
	{
		// Hell nothing..
	}

}
