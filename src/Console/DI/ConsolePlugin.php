<?php declare(strict_types = 1);

namespace Apitte\Console\DI;

use Apitte\Console\Command\RouteDumpCommand;
use Apitte\Core\DI\Plugin\Plugin;

class ConsolePlugin extends Plugin
{

	public static function getName(): string
	{
		return 'console';
	}

	public function beforePluginCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('console'))
			->setFactory(RouteDumpCommand::class);
	}

}
