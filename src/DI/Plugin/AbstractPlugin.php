<?php

namespace Apitte\Core\DI\Plugin;

use Nette\DI\ContainerBuilder;
use Nette\PhpGenerator\ClassType;

abstract class AbstractPlugin implements Plugin
{

	/** @var PluginCompiler */
	protected $compiler;

	/** @var string */
	protected $name;

	/** @var array */
	protected $config = [];

	/**
	 * @param PluginCompiler $compiler
	 */
	public function __construct(PluginCompiler $compiler)
	{
		$this->compiler = $compiler;
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
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
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
		return $this->compiler->getExtension()->prefix($this->name . '.' . $id);
	}

	/**
	 * @param string $id
	 * @return string
	 */
	protected function extensionPrefix($id)
	{
		return $this->compiler->getExtension()->prefix($id);
	}

	/**
	 * @return ContainerBuilder
	 */
	protected function getContainerBuilder()
	{
		return $this->compiler->getExtension()->getContainerBuilder();
	}

	/**
	 * @param array $expected
	 * @param array $config
	 * @return array
	 */
	protected function setupConfig(array $expected, array $config)
	{
		return $this->config = $this->compiler->getExtension()->validateConfig($expected, $config, $this->name);
	}

}
