<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Logical;

use Apitte\Core\Exception\LogicalException;

final class InvalidDependencyException extends LogicalException
{

	public static function missing(string $class, string $composer): self
	{
		return new self(sprintf('Missing class "%s". Try to install composer dependency "%s"', $class, $composer));
	}

}
