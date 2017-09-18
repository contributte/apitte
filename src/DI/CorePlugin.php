<?php

namespace Apitte\Core\DI;

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Dispatcher\ApiDispatcher;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Router\ApiRouter;
use Apitte\Core\Router\IRouter;
use Apitte\Core\Schema\ApiSchema;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Apitte\Core\Schema\Serialization\ArraySerializator;
use Apitte\Core\Schema\Validation\PathValidation;
use Apitte\Core\Schema\Validation\RootPathValidation;
use Apitte\Core\Schema\Validator\SchemaBuilderValidator;
use Apitte\Core\UI\IHandler;
use Apitte\Core\UI\ServiceHandler;
use Nette\DI\CompilerExtension;

class CorePlugin extends AbstractPlugin
{

	const PLUGIN_NAME = 'core';

	// Loader types
	const LOADERS = ['annotations', 'neon', 'php'];

	/** @var array */
	protected $defaults = [
		'loader' => 'annotations',
	];

	/**
	 * @param CompilerExtension $extension
	 */
	public function __construct(CompilerExtension $extension)
	{
		parent::__construct($extension);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Process and validate config
	 *
	 * @param array $config
	 * @return void
	 */
	public function setupPlugin(array $config = [])
	{
		$this->processConfig($this->defaults, $config);
	}

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadPluginConfiguration()
	{
		// Receive container builder
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('dispatcher'))
			->setClass(IDispatcher::class)
			->setFactory(ApiDispatcher::class);

		$builder->addDefinition($this->prefix('router'))
			->setClass(IRouter::class)
			->setFactory(ApiRouter::class);

		$builder->addDefinition($this->prefix('handler'))
			->setClass(IHandler::class)
			->setFactory(ServiceHandler::class);

		$builder->addDefinition($this->prefix('schema'))
			->setClass(ApiSchema::class);
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforePluginCompile()
	{
		// Receive container builder
		$builder = $this->getContainerBuilder();

		// Receive schema builder
		$schemaBuilder = $this->getSchemaBuilder();

		// Validate schema
		$this->validateSchema($schemaBuilder);

		// Convert schema to array (for DI)
		$generator = new ArraySerializator();
		$schema = $generator->serialize($schemaBuilder);

		// Register services
		$builder->addDefinition($this->prefix('hydrator'))
			->setClass(ArrayHydrator::class);

		$builder->getDefinition($this->prefix('schema'))
			->setFactory('@' . $this->prefix('hydrator') . '::hydrate', [$schema]);
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return SchemaBuilder
	 */
	protected function getSchemaBuilder()
	{
		$config = $this->config;

		// Create loader and fill schema builder
		if ($config['loader'] === 'annotations') {
			return $this->loadAnnotations();
		} else if ($config['loader'] === 'neon') {
			throw new InvalidStateException('Not implemented');
		} else if ($config['loader'] === 'php') {
			throw new InvalidStateException('Not implemented');
		} else {
			throw new InvalidStateException('Unknown loader type');
		}
	}

	/**
	 * Create annotation loaders and create SchemaBuilder
	 *
	 * @return SchemaBuilder
	 */
	protected function loadAnnotations()
	{
		$builder = $this->getContainerBuilder();
		$loader = new DoctrineAnnotationLoader($builder);

		return $loader->load();
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	protected function validateSchema($builder)
	{
		$validator = new SchemaBuilderValidator();
		$validator->add(new RootPathValidation());
		$validator->add(new PathValidation());

		$validator->validate($builder);
	}

}
