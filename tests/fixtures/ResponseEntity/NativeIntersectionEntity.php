<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use ArrayAccess;
use DateTime; // phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse

class NativeIntersectionEntity
{

	// PHPCS doesn't understand intersections :-(
	// phpcs:ignore Squiz.WhiteSpace.OperatorSpacing.NoSpaceBeforeAmp, Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfterAmp
	public DateTime&ArrayAccess $intersection;

}
