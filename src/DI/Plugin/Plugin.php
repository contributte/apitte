<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Nette\PhpGenerator\ClassType;

interface Plugin
{

	/**
	 * @param mixed[] $config
	 */
	public function setupPlugin(array $config = []): void;

	public function loadPluginConfiguration(): void;

	public function beforePluginCompile(): void;

	public function afterPluginCompile(ClassType $class): void;

}
