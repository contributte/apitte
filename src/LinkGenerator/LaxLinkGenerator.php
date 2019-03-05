<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

use Apitte\Core\DI\Plugin\CoreMappingPlugin;
use Apitte\Core\Schema\Endpoint;
use RuntimeException;

/**
 * Little dump LinkGenerator which does not require CoreMappingPlugin
 */
class LaxLinkGenerator extends BaseLinkGenerator
{

	/**
	 * @param mixed[] $parameters
	 */
	protected function buildUrl(Endpoint $endpoint, array $parameters, string $fragment): string
	{
		throw new RuntimeException(sprintf('Not implemented yet, sorry. You could try strict variant with "%s" activated', CoreMappingPlugin::class));
		//TODO
		//  - replace parameters in path, check if all are replaced
		//	- put other parameters in query

		$maskParameters = preg_match('#{(.*?)}#', $endpoint->getMask());

		var_dump($maskParameters);

		$mask = preg_replace_callback(
			'#{(.*?)}#',
			function ($match) use ($pathParameters) {
				return $pathParameters[$match[1]];
			},
			(string) $endpoint->getMask()
		);

		$query = '';
		return $this->getBaseUri() . $mask . $query . $fragment;
	}

}
