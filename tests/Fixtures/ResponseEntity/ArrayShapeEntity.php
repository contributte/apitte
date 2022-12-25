<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

class ArrayShapeEntity
{

	/** @var array<string,int> */
	public array $shapeOfStringToInt;

	/** @var array<string,SinglePropertyEntity> */
	public array $shapeOfStringToObject;

}
