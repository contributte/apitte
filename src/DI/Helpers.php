<?php declare(strict_types = 1);

namespace Apitte\Core\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

class Helpers
{

	/**
	 * @param mixed[] $definitions
	 * @return mixed[]
	 */
	public static function sort(array $definitions, int $default = 10): array
	{
		// Sort by priority
		uasort($definitions, function (array $a, array $b) use ($default) {
			$p1 = $a['priority'] ?? $default;
			$p2 = $b['priority'] ?? $default;

			if ($p1 === $p2) {
				return 0;
			}

			return ($p1 < $p2) ? -1 : 1;
		});

		return $definitions;
	}

	/**
	 * @param mixed[] $definitions
	 * @return ServiceDefinition[]
	 */
	public static function getDefinitions(array $definitions, ContainerBuilder $builder): array
	{
		return array_map(function ($name) use ($builder) {
			return $builder->getDefinition($name);
		}, array_keys($definitions));
	}

}
