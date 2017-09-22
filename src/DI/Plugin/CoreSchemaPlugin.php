<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Apitte\Core\Schema\Serialization\ArraySerializator;
use Apitte\Core\Schema\Validation\GroupPathValidation;
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
		// Receive container builder
		$builder = $this->getContainerBuilder();

		// Register services
		$builder->addDefinition($this->prefix('hydrator'))
			->setClass(ArrayHydrator::class);

		$builder->getDefinition($this->extensionPrefix('core.schema'))
			->setFactory('@' . $this->prefix('hydrator') . '::hydrate', [$this->compileSchema()]);
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return array
	 */
	protected function compileSchema()
	{
		// Instance schema builder
		$builder = new SchemaBuilder();

		// Load schema
		$builder = $this->loadSchema($builder);

		// Validate schema
		$builder = $this->validateSchema($builder);

		// Convert schema to array (for DI)
		$generator = new ArraySerializator();
		$schema = $generator->serialize($builder);

		return $schema;
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return SchemaBuilder
	 */
	protected function loadSchema(SchemaBuilder $builder)
	{
		// Load schema from...
		if ($this->config['loader'] === 'annotations') {
			$loader = new DoctrineAnnotationLoader($this->getContainerBuilder());

			return $loader->load($builder);
		} else if ($this->config['loader'] === 'neon') {
			throw new InvalidStateException('Not implemented');
		} else if ($this->config['loader'] === 'php') {
			throw new InvalidStateException('Not implemented');
		} else {
			throw new InvalidStateException('Unknown loader type');
		}
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return SchemaBuilder
	 */
	protected function validateSchema(SchemaBuilder $builder)
	{
		$validator = new SchemaBuilderValidator();
		$validator->add(new RootPathValidation());
		$validator->add(new PathValidation());
		$validator->add(new GroupPathValidation());

		$validator->validate($builder);

		return $builder;
	}

}
