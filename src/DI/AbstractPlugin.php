<?php

namespace Apitte\Core\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\PhpGenerator\ClassType;

abstract class AbstractPlugin implements Plugin
{

	/** @var CompilerExtension */
	protected $extension;

	/** @var string */
	protected $name;

	/** @var array */
	protected $config = [];

	/**
	 * @param CompilerExtension $extension
	 */
	public function __construct(CompilerExtension $extension)
	{
		$this->extension = $extension;
	}

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * PLUGIN EXTENSION ********************************************************
	 */

	/**
	 * @param array $config
	 * @return void
	 */
	public function setupPlugin(array $config = [])
	{
	}

	/**
	 * @return void
	 */
	public function loadPluginConfiguration()
	{
	}

	/**
	 * @return void
	 */
	public function beforePluginCompile()
	{
	}

	/**
	 * @param ClassType $class
	 * @return void
	 */
	public function afterPluginCompile(ClassType $class)
	{
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param string $id
	 * @return string
	 */
	protected function prefix($id)
	{
		return $this->extension->prefix($this->name . $id);
	}

	/**
	 * @return ContainerBuilder
	 */
	protected function getContainerBuilder()
	{
		return $this->extension->getContainerBuilder();
	}

	/**
	 * @param array $expected
	 * @param array $config
	 * @return array
	 */
	protected function processConfig(array $expected, array $config)
	{
		return $this->config = $this->extension->validateConfig($expected, $config);
	}

	/**
	 * @return array
	 */
	protected function getExtensionConfig()
	{
		return $this->extension->getConfig();
	}

}
