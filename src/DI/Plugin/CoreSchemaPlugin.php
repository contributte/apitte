<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Apitte\Core\Schema\Serialization\ArraySerializator;
use Apitte\Core\Schema\Validation\PathValidation;
use Apitte\Core\Schema\Validation\RootPathValidation;
use Apitte\Core\Schema\Validator\SchemaBuilderValidator;

class CoreSchemaPlugin extends AbstractPlugin
{

	const PLUGIN_NAME = 'schema';

	// Loader types
	const LOADERS = ['annotations', 'neon', 'php'];

	/** @var array */
	protected $defaults = [
		'loader' => 'annotations',
	];

	/**
	 * @param PluginCompiler $compiler
	 */
	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
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
		$this->setupConfig($this->defaults, $config);
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforePluginCompile()
	{
		// Receive schema builder
		$schemaBuilder = $this->getSchemaBuilder();

		// Validate schema
		$this->validateSchema($schemaBuilder);

		// Convert schema to array (for DI)
		$generator = new ArraySerializator();
		$schema = $generator->serialize($schemaBuilder);

		// =======================================

		// Receive container builder
		$builder = $this->getContainerBuilder();

		// Register services
		$builder->addDefinition($this->prefix('hydrator'))
			->setClass(ArrayHydrator::class);

		$builder->getDefinition($this->extensionPrefix('core.schema'))
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
