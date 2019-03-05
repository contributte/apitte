<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

interface LinkGenerator
{

	/**
	 * @param string  $destination "[[module:]controller:action]"
	 * @param mixed[] $parameters
	 */
	public function link(string $destination, array $parameters = []): string;

}
