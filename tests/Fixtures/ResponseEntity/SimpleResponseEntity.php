<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use DateTime;

class SimpleResponseEntity
{

	public int $int;

	public ?float $nullableFloat = null;

	public string $string;

	public bool $bool;

	public DateTime $datetime;

	public mixed $mixed;

	public $untypedProperty;

}
