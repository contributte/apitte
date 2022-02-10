<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

class CompoundResponseEntity
{

	/** @var SimpleResponseEntity[]|null */
	public $nullableObjects;

	/** @var string[]|int[] */
	public $unionProperties = [];

	/** @var string[]&int[] */
	public $intersectionProperties = [];

	/** @var string[]|(int[]&float[]) */
	public $unionAndIntersectionProperties;

}
