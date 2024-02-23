<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mapping\Validator;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class SimpleEntity extends BasicEntity
{

	/**
	 * @Assert\NotNull()
	 * @Assert\Type(
	 *     type="integer",
	 *     message="The value {{ value }} is not a valid {{ type }}."
	 * )
	 */
	public int $id;

	#[Assert\NotNull]
	#[Assert\Type(type: 'integer')]
	public $typedId1;

	#[Assert\NotNull]
	#[Assert\Type(type: 'integer')]
	public int $typedId2;

	#[Assert\Type(type: 'integer')]
	public ?int $typedId3;

	#[Assert\Type(type: 'integer')]
	public ?int $typedId4 = null;

}
