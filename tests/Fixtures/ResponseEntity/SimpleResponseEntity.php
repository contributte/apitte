<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use DateTime;

class SimpleResponseEntity
{

	/** @var int */
	public $int;

	/** @var float|null */
	public $nullableFloat;

	/** @var string */
	public $string;

	/** @var bool */
	public $bool;

	/** @var DateTime */
	public $datetime;

	/** @var mixed */
	public $mixed;

	// phpcs:ignore
	public $untypedProperty;

}
