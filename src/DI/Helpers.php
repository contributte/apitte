<?php

namespace Apitte\Core\DI;

use Nette\DI\ContainerBuilder;

class Helpers
{

	/**
	 * @param array $definitions
	 * @return array
	 */
	public static function sort(array $definitions, $default = 10)
	{
		// Sort by priority
		uasort($definitions, function ($a, $b) use ($default) {
			$p1 = isset($a['priority']) ? $a['priority'] : $default;
			$p2 = isset($b['priority']) ? $b['priority'] : $default;

			if ($p1 == $p2) {
				return 0;
			}

			return ($p1 < $p2) ? -1 : 1;
		});

		return $definitions;
	}

	/**
	 * @param array $definitions
	 * @param ContainerBuilder $builder
	 * @return array
	 */
	public static function getDefinitions(array $definitions, ContainerBuilder $builder)
	{
		return array_map(function ($name) use ($builder) {
			return $builder->getDefinition($name);
		}, array_keys($definitions));
	}

}
