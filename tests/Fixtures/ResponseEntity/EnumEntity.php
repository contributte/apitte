<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

class EnumEntity
{

	public StringBackedEnum $string;

	public IntBackedEnum $int;

	public PureEnum $pure;

	public ?StringBackedEnum $nullable;

}
