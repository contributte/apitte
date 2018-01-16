<?php

namespace Apitte\Core\Mapping\Validator;

use Apitte\Core\Exception\Api\ValidationException;

interface IEntityValidator
{

	/**
	 * @param object $entity
	 * @throws ValidationException
	 * @return void
	 */
	public function validate($entity);

}
