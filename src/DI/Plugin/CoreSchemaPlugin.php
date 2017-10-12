<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Apitte\Core\Schema\Serialization\ArraySerializator;
use Apitte\Core\Schema\Validation\ControllerPathValidation;
use Apitte\Core\Schema\Validation\GroupPathValidation;
use Apitte\Core\Schema\Validation\IdValidation;
use Apitte\Core\Schema\Validation\IValidation;
use Apitte\Core\Schema\Validation\PathValidation;
use Apitte\Core\Schema\Validator\SchemaBuilderValidator;

class CoreSchemaPlugin extends AbstractPlugin
{

	const PLUGIN_NAME = 'schema';

	// Loader types
	const LOADERS = ['annotations', 'neon', 'php'];

	/** @var IValidation[] */
	public static $validations = [
		'controllerPath' => ControllerPathValidation::class,
		'groupPath' => GroupPathValidation::class,
		'path' => PathValidation::class,
		'id' => IdValidation::class,
	];

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
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforePluginCompile()
	{
		// Receive container builder
		$builder = $this->getContainerBuilder();

		// Register services
		$builder->addDefinition($this->extensionPrefix('core.schema.hydrator'))
			->setClass(ArrayHydrator::class);

		$builder->getDefinition($this->extensionPrefix('core.schema'))
			->setFactory('@' . $this->extensionPrefix('core.schema.hydrator') . '::hydrate', [$this->compileSchema()]);
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

		// Add all validators at compile-time
		foreach (self::$validations as $validation) {
			$validator->add(new $validation);
		}

		// Validate schema
		$validator->validate($builder);

		return $builder;
	}

}
