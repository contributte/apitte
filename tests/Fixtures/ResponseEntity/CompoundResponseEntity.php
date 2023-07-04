<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

class CompoundResponseEntity
{

	/** @var SimpleResponseEntity[]|null */
	public ?array $nullableObjects = null;

	/** @var string[]|int[] */
	public array $unionProperties = [];

	/** @var string[]&int[] */
	public array $intersectionProperties = [];

	/** @var string[]|(int[]&float[]) */
	public $unionAndIntersectionProperties;

}
