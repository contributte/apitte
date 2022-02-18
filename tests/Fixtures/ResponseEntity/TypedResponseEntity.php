<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use DateTime;

class TypedResponseEntity
{

	public int $int;

	public ?float $nullableFloat;

	public string $string;

	public bool $bool;

	public DateTime $datetime;

	/** @var int */
	public $phpdocInt;

	// phpcs:ignore
	public $untypedProperty;

	public array $untypedArray;

	/** @var int[] */
	public array $arrayOfInt;

}
