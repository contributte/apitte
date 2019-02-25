<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\UI\Controller\IController;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

abstract class AbstractContainerLoader implements ILoader
{

	/** @var ContainerBuilder */
	private $builder;

	public function __construct(ContainerBuilder $builder)
	{
		$this->builder = $builder;
	}

	/**
	 * Find controllers in container definitions
	 *
	 * @return ServiceDefinition[]
	 */
	protected function findControllers(): array
	{
		return $this->builder->findByType(IController::class);
	}

}
