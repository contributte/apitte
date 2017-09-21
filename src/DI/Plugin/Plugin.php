<?php

namespace Apitte\Core\DI\Plugin;

use Nette\PhpGenerator\ClassType;

interface Plugin
{

	/**
	 * @param array $config
	 * @return void
	 */
	public function setupPlugin(array $config = []);

	/**
	 * @return void
	 */
	public function loadPluginConfiguration();

	/**
	 * @return void
	 */
	public function beforePluginCompile();

	/**
	 * @param ClassType $class
	 * @return void
	 */
	public function afterPluginCompile(ClassType $class);

}
