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
			$p1 = is_array($tag1) && isset($tag1['priority']) ? (int) $tag1['priority'] : $default;

			$tag2 = $b->getTag($tagname);
			$p2 = is_array($tag2) && isset($tag2['priority']) ? (int) $tag2['priority'] : $default;

			return $p1 <=> $p2;
		});

		return $definitions;
	}

}
