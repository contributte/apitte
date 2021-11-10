<?php declare(strict_types = 1);

namespace Apitte\Presenter;

use Nette\Application\Routers\Route;

class ApiRoute extends Route
{

	public const APITTE_MODULE = 'Apitte:Api';

	/**
	 * @param mixed[] $metadata
	 */
	public function __construct(string $prefix, array $metadata = [], int $flags = 0)
	{
		if ($metadata === []) {
			$metadata['presenter'] = self::APITTE_MODULE;
		}

		parent::__construct(rtrim($prefix, '/') . '/<path .*>', $metadata, $flags);
	}

}
