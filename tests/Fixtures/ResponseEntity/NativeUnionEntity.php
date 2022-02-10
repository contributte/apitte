<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use DateTime;

class NativeUnionEntity
{

	public string|int $stringOrInt;

	public bool|null $nullableBool;

	public DateTime|int $dateOrInt;

}
