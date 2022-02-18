<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

class MixedEntity
{

	public mixed $mixed;

	/** @var string[]|(int[]&float[]) */
	public mixed $complexType;

}
