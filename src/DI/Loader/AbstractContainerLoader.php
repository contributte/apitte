<?php

namespace Apitte\Core\DI\Loader;

use Apitte\Core\UI\Controller\IController;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

abstract class AbstractContainerLoader implements ILoader
{

	/** @var ContainerBuilder */
	private $builder;

	/**
	 * @param ContainerBuilder $builder
	 */
	public function __construct(ContainerBuilder $builder)
	{
		$this->builder = $builder;
	}

	/**
	 * Find controllers in container definitions
	 *
	 * @return ServiceDefinition[]
	 */
	protected function findControllers()
	{
		return $this->builder->findByType(IController::class);
	}

	/**
	 * @param ServiceDefinition[] $definitions
	 * @return void
	 */
	protected function addDependencies(array $definitions)
	{
		foreach ($definitions as $def) {
			$this->builder->addDependency($def->getClass());
		}
	}

}
