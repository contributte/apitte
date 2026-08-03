<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use ArrayAccess;
use DateTime; // phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse

class NullableCompoundEntity
{

	/** @var string[]|int[]|null */
	public ?array $nullableUnion = null;

	public (DateTime&ArrayAccess)|null $nullableIntersection = null;

}
