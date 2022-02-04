<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mapping\Validator;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class SimpleEntity extends BasicEntity
{

	/**
	 * @var int
	 * @Assert\NotNull()
	 * @Assert\Type(
	 *     type="integer",
	 *     message="The value {{ value }} is not a valid {{ type }}."
	 * )
	 */
	public $id;

}
