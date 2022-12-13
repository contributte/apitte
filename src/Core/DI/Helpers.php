<?php declare(strict_types = 1);

namespace Apitte\Core\DI;

use Nette\DI\Definitions\Definition;

class Helpers
{

	/**
	 * @param Definition[] $definitions
	 * @return Definition[]
	 */
	public static function sortByPriorityInTag(string $tagname, array $definitions, int $default = 10): array
	{
		// Sort by priority
		uasort($definitions, static function (Definition $a, Definition $b) use ($tagname, $default): int {
			$tag1 = $a->getTag($tagname);
			$p1 = $tag1 !== null && isset($tag1['priority']) ? $tag1['priority'] : $default;

			$tag2 = $b->getTag($tagname);
			$p2 = $tag2 !== null && isset($tag2['priority']) ? $tag2['priority'] : $default;

			if ($p1 === $p2) {
				return 0;
			}

			return ($p1 < $p2) ? -1 : 1;
		});

		return $definitions;
	}

}
