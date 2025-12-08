<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\UI\Controller\IController;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;

abstract class AbstractContainerLoader implements ILoader
{

	public function __construct(
		private readonly ContainerBuilder $builder,
	)
	{
	}

	/**
	 * Find controllers in container definitions
	 *
	 * @return Definition[]
	 */
	protected function findControllers(): array
	{
		return $this->builder->findByType(IController::class);
	}

}
