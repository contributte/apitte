<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\Loader\AttributeLoader;
use Apitte\Core\DI\Loader\ILoader;
use Apitte\Core\DI\Loader\NeonLoader;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Serialization\ArrayHydrator;
use Apitte\Core\Schema\Serialization\ArraySerializer;
use Apitte\Core\Schema\Serialization\IDecorator;
use Apitte\Core\Schema\Validation\ControllerPathValidation;
use Apitte\Core\Schema\Validation\ControllerValidation;
use Apitte\Core\Schema\Validation\FullpathValidation;
use Apitte\Core\Schema\Validation\GroupPathValidation;
use Apitte\Core\Schema\Validation\IdValidation;
use Apitte\Core\Schema\Validation\IValidation;
use Apitte\Core\Schema\Validation\NegotiationValidation;
use Apitte\Core\Schema\Validation\PathValidation;
use Apitte\Core\Schema\Validation\RequestBodyValidation;
use Apitte\Core\Schema\Validation\RequestParameterValidation;
use Apitte\Core\Schema\Validator\SchemaBuilderValidator;
use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Utils\Arrays;
use stdClass;

/**
 * @property-read stdClass $config
 */
class CoreSchemaPlugin extends Plugin
{

	/** @var IDecorator[] */
	public static array $decorators = [];

	public static function getName(): string
	{
		return 'schema';
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

		$schemaDefinition = $builder->getDefinition($this->extensionPrefix('core.schema'));
		assert($schemaDefinition instanceof ServiceDefinition);
		$schemaDefinition->setFactory('@' . $this->extensionPrefix('core.schema.hydrator') . '::hydrate', [$this->compileSchema()]);
	}

	protected function getConfigSchema(): Schema
	{
		return Expect::structure([
			'loaders' => Expect::structure([
				'annotations' => Expect::structure([
					'enable' => Expect::bool(true),
					'loader' => Expect::string(AttributeLoader::class),
				]),
				'neon' => Expect::structure([
					'enable' => Expect::bool(false),
					'files' => Expect::arrayOf('string'),
				]),
			]),
			'schema' => Expect::array(),
			'validations' => Expect::array()->default([
				'controller' => ControllerValidation::class,
				'controllerPath' => ControllerPathValidation::class,
				'fullPath' => FullpathValidation::class,
				'groupPath' => GroupPathValidation::class,
				'id' => IdValidation::class,
				'negotiation' => NegotiationValidation::class,
				'path' => PathValidation::class,
				'requestBody' => RequestBodyValidation::class,
			]),
		]);
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
		$generator = new ArraySerializer();

		return $generator->serialize($builder);
	}

	protected function loadSchema(SchemaBuilder $builder): SchemaBuilder
	{
		$loaders = $this->config->loaders;

		if ($loaders->annotations->enable) {
			if (!class_exists($loaders->annotations->loader)) {
				throw new \RuntimeException(sprintf('Annotation loader class %s does not exist', $loaders->annotations->loader));
			}

			if (!is_subclass_of($loaders->annotations->loader, ILoader::class)) {
				throw new \RuntimeException(sprintf('Annotation loader class %s must be subclass of %s', $loaders->annotations->loader, ILoader::class));
			}

			$loader = new $loaders->annotations->loader($this->getContainerBuilder());
			$builder = $loader->load($builder);
		}

		if ($loaders->neon->enable) {
			$files = $loaders->neon->files;

			// Load schema from files
			$adapter = new NeonAdapter();
			$schema = [];

			foreach ($files as $file) {
				$schema = Arrays::mergeTree($schema, $adapter->load($file));
			}

			$loader = new NeonLoader($schema);
			$builder = $loader->load($builder);
		}

		return $builder;
	}

	protected function validateSchema(SchemaBuilder $builder): SchemaBuilder
	{
		/** @var class-string<IValidation>[] $validations */
		$validations = $this->config->validations;

		$validator = new SchemaBuilderValidator();

		// Add all validators at compile-time
		foreach ($validations as $validation) {
			$validator->add(new $validation());
		}

		/** @var ?CoreMappingPlugin $coreMappingPlugin */
		$coreMappingPlugin = $this->compiler->getPlugin(CoreMappingPlugin::getName());

		if ($coreMappingPlugin !== null) {
			$validator->add(new RequestParameterValidation($coreMappingPlugin->getAllowedTypes()));
		}

		// Validate schema
		$validator->validate($builder);

		return $builder;
	}

}
