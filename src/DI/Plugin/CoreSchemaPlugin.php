<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Apitte\Core\Schema\Serialization\ArraySerializator;
use Apitte\Core\Schema\Serialization\IDecorator;
use Apitte\Core\Schema\Validation\ControllerPathValidation;
use Apitte\Core\Schema\Validation\FullpathValidation;
use Apitte\Core\Schema\Validation\GroupPathValidation;
use Apitte\Core\Schema\Validation\IdValidation;
use Apitte\Core\Schema\Validation\NegotiationValidation;
use Apitte\Core\Schema\Validation\PathValidation;
use Apitte\Core\Schema\Validation\RequestMapperValidation;
use Apitte\Core\Schema\Validation\RequestParameterValidation;
use Apitte\Core\Schema\Validation\ResponseMapperValidation;
use Apitte\Core\Schema\Validator\SchemaBuilderValidator;

class CoreSchemaPlugin extends AbstractPlugin
{

	public const PLUGIN_NAME = 'schema';

	// Loader types
	public const LOADERS = ['annotations', 'neon', 'php'];

	/** @var IDecorator[] */
	public static $decorators = [];

	/** @var mixed[] */
	protected $defaults = [
		'loader' => 'annotations',
		'validations' => [
			'controllerPath' => ControllerPathValidation::class,
			'fullPath' => FullpathValidation::class,
			'groupPath' => GroupPathValidation::class,
			'id' => IdValidation::class,
			'negotiation' => NegotiationValidation::class,
			'path' => PathValidation::class,
			'requestMapper' => RequestMapperValidation::class,
			'responseMapper' => ResponseMapperValidation::class,
		],
	];

	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Decorate services
	 */
	public function beforePluginCompile(): void
	{
		// Receive container builder
		$builder = $this->getContainerBuilder();

		// Register services
		$builder->addDefinition($this->extensionPrefix('core.schema.hydrator'))
			->setFactory(ArrayHydrator::class);

		$builder->getDefinition($this->extensionPrefix('core.schema'))
			->setFactory('@' . $this->extensionPrefix('core.schema.hydrator') . '::hydrate', [$this->compileSchema()]);
	}

	/**
	 * @return mixed[]
	 */
	protected function compileSchema(): array
	{
		// Instance schema builder
		$builder = new SchemaBuilder();

		// Load schema
		$builder = $this->loadSchema($builder);

		// Validate schema
		$builder = $this->validateSchema($builder);

		// Update schema at compile-time
		foreach (self::$decorators as $decorator) {
			$decorator->decorate($builder);
		}

		// Convert schema to array (for DI)
		$generator = new ArraySerializator();
		return $generator->serialize($builder);
	}

	protected function loadSchema(SchemaBuilder $builder): SchemaBuilder
	{
		// Load schema from...
		if ($this->config['loader'] === 'annotations') {
			$loader = new DoctrineAnnotationLoader($this->getContainerBuilder());

			return $loader->load($builder);
		}

		if ($this->config['loader'] === 'neon') {
			throw new InvalidStateException('Not implemented');
		}

		if ($this->config['loader'] === 'php') {
			throw new InvalidStateException('Not implemented');
		}

		throw new InvalidStateException('Unknown loader type');
	}

	protected function validateSchema(SchemaBuilder $builder): SchemaBuilder
	{
		$validations = $this->config['validations'];

		$coreMappingPlugin = $this->compiler->getPlugin(CoreMappingPlugin::PLUGIN_NAME);
		if ($coreMappingPlugin !== null) {
			$validations['requestParameter'] = RequestParameterValidation::class;
		}

		$validator = new SchemaBuilderValidator();

		// Add all validators at compile-time
		foreach ($validations as $validation) {
			$validator->add(new $validation());
		}

		// Validate schema
		$validator->validate($builder);

		return $builder;
	}

}
