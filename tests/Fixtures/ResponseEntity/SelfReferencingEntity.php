<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

class SelfReferencingEntity
{

	public self $selfReference;

	/** @var static */
	public $staticReference;

	public SelfReferencingEntity $classNameReference;

	public string $normalProperty;

}
