<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Annotation\Controller\Id;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Negotiation;
use Apitte\Core\Annotation\Controller\OpenApi;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method as SchemaMethod;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\UI\Controller\IController;
use Nette\Neon\Neon;
use ReflectionClass;
use ReflectionMethod;

class AttributeLoader extends AbstractContainerLoader
{

	/** @var mixed[] */
	private array $meta = [
		'services' => [],
	];

	public function load(SchemaBuilder $builder): SchemaBuilder
	{
		// Find all controllers by type (interface, annotation)
		$controllers = $this->findControllers();

		// Iterate over all controllers
		foreach ($controllers as $def) {
			/** @var class-string|null $type */
			$type = $def->getType();

			if ($type === null) {
				throw new InvalidStateException('Cannot analyse class with no type defined. Make sure all controllers have defined their class.');
			}

			// Analyse all parent classes
			$class = $this->analyseClass($type);

			// Check if a controller or his abstract implements IController,
			// otherwise, skip this controller
			if (!$this->acceptController($class)) {
				continue;
			}

			// Create scheme endpoint
			$schemeController = $builder->addController($type);

			// Parse #[Path], #[Id]
			$this->parseControllerClassAttributes($schemeController, $class);

			// Parse #[Method], #[Path]
			$this->parseControllerMethodsAttributes($schemeController, $class);
		}

		return $builder;
	}

	/**
	 * @param class-string $class
	 * @return ReflectionClass<object>
	 */
	protected function analyseClass(string $class): ReflectionClass
	{
		// Analyse only new-ones
		if (isset($this->meta['services'][$class])) {
			return $this->meta['services'][$class]['reflection'];
		}

		// Create reflection
		$classRef = new ReflectionClass($class);

		// Index controller as service
		$this->meta['services'][$class] = [
			'reflection' => $classRef,
			'parents' => [],
		];

		// Get all parents
		$parents = class_parents($class);
		$reflections = [];

		if ($parents === false) {
			$parents = [];
		}

		// Iterate over all parents and analyse them
		foreach ($parents as $parentClass) {
			// Stop multiple analysing
			if (isset($this->meta['services'][$parentClass])) {
				// Just reference it in reflections
				$reflections[$parentClass] = $this->meta['services'][$parentClass]['reflection'];

				continue;
			}

			// Create reflection for parent class
			$parentClassRf = new ReflectionClass($parentClass);
			$reflections[$parentClass] = $parentClassRf;

			// Index service
			$this->meta['services'][$parentClass] = [
				'reflection' => $parentClassRf,
				'parents' => [],
			];

			// Analyse parent (recursive)
			$this->analyseClass($parentClass);
		}

		// Append all parents to this service
		$this->meta['services'][$class]['parents'] = $reflections;

		return $classRef;
	}

	/**
	 * @param ReflectionClass<object> $class
	 */
	protected function acceptController(ReflectionClass $class): bool
	{
		return is_subclass_of($class->getName(), IController::class);
	}

	/**
	 * @param ReflectionClass<object> $class
	 */
	protected function parseControllerClassAttributes(Controller $controller, ReflectionClass $class): void
	{
		// Read class attributes
		$attributes = $this->getClassAttributes($class);

		// Iterate over all class attributes in controller
		foreach ($attributes as $attribute) {
			// Parse #[Path] =======================
			if ($attribute instanceof Path) {
				$controller->setPath($attribute->getPath());

				continue;
			}

			// Parse #[Id] =========================
			if ($attribute instanceof Id) {
				$controller->setId($attribute->getName());
			}

			// Parse #[Tag] ==================================
			if ($attribute instanceof Tag) {
				$controller->addTag($attribute->getName(), $attribute->getValue());
			}

			// Parse #[OpenApi] ============================
			if ($attribute instanceof OpenApi) {
				$controller->setOpenApi(Neon::decode($attribute->getData()) ?? []);

				continue;
			}
		}

		// Reverse order
		$reversed = array_reverse($this->meta['services'][$class->getName()]['parents']);

		// Iterate over all class attributes in controller's parents
		foreach ($reversed as $parent) {
			// Read parent class attributes
			$parentAttributes = $this->getClassAttributes($parent);

			// Iterate over all parent class attributes
			foreach ($parentAttributes as $attribute) {
				// Parse #[Id] ==========================
				if ($attribute instanceof Id) {
					$controller->addGroupId($attribute->getName());
				}

				// Parse #[Path] ========================
				if ($attribute instanceof Path) {
					$controller->addGroupPath($attribute->getPath());
				}

				// Parse #[Tag] ==============================
				if ($attribute instanceof Tag) {
					$controller->addTag($attribute->getName(), $attribute->getValue());
				}
			}
		}
	}

	/**
	 * @param ReflectionClass<object> $reflectionClass
	 */
	protected function parseControllerMethodsAttributes(Controller $controller, ReflectionClass $reflectionClass): void
	{
		// Iterate over all methods in class
		foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			// Read method attributes
			$attributes = $this->getMethodAttributes($method);

			// Skip if method has no #[Path]/#[Method] attributes
			if (count($attributes) <= 0) {
				continue;
			}

			// Append method to scheme
			$schemaMethod = $controller->addMethod($method->getName());

			// Iterate over all method attributes
			foreach ($attributes as $attribute) {
				// Parse #[Path] =============================
				if ($attribute instanceof Path) {
					$schemaMethod->setPath($attribute->getPath());

					continue;
				}

				// Parse #[Method] ===========================
				if ($attribute instanceof Method) {
					$schemaMethod->addHttpMethods($attribute->getMethods());

					continue;
				}

				// Parse #[Tag] ==============================
				if ($attribute instanceof Tag) {
					$schemaMethod->addTag($attribute->getName(), $attribute->getValue());

					continue;
				}

				// Parse #[Id] ===============================
				if ($attribute instanceof Id) {
					$schemaMethod->setId($attribute->getName());

					continue;
				}

				// Parse #[RequestParameter] ================
				if ($attribute instanceof RequestParameter) {
					$this->addEndpointParameterToSchemaMethod($schemaMethod, $attribute);

					continue;
				}

				// Parse #[Response] ================
				if ($attribute instanceof Response) {
					$this->addResponseToSchemaMethod($schemaMethod, $attribute);

					continue;
				}

				// Parse #[RequestBody] ================
				if ($attribute instanceof RequestBody) {
					$requestBody = new EndpointRequestBody();
					$requestBody->setDescription($attribute->getDescription());
					$requestBody->setEntity($attribute->getEntity());
					$requestBody->setRequired($attribute->isRequired());
					$requestBody->setValidation($attribute->isValidation());
					$schemaMethod->setRequestBody($requestBody);

					continue;
				}

				// Parse #[OpenApi] ================
				if ($attribute instanceof OpenApi) {
					$schemaMethod->setOpenApi(Neon::decode($attribute->getData()) ?? []);

					continue;
				}

				// Parse #[Negotiation] =====================
				if ($attribute instanceof Negotiation) {
					$this->addNegotiationToSchemaMethod($schemaMethod, $attribute);
				}
			}
		}
	}

	/**
	 * @param ReflectionClass<object> $class
	 * @return object[]
	 */
	protected function getClassAttributes(ReflectionClass $class): array
	{
		$attributes = [];

		foreach ($class->getAttributes() as $attribute) {
			$attributes[] = $attribute->newInstance();
		}

		return $attributes;
	}

	/**
	 * @return object[]
	 */
	protected function getMethodAttributes(ReflectionMethod $method): array
	{
		$attributes = [];

		foreach ($method->getAttributes() as $attribute) {
			$attributes[] = $attribute->newInstance();
		}

		return $attributes;
	}

	private function addEndpointParameterToSchemaMethod(SchemaMethod $schemaMethod, RequestParameter $requestParameter): void
	{
		$endpointParameter = $schemaMethod->addParameter($requestParameter->getName(), $requestParameter->getType());

		$endpointParameter->setDescription($requestParameter->getDescription());
		$endpointParameter->setIn($requestParameter->getIn());
		$endpointParameter->setRequired($requestParameter->isRequired());
		$endpointParameter->setDeprecated($requestParameter->isDeprecated());
		$endpointParameter->setAllowEmpty($requestParameter->isAllowEmpty());
		$endpointParameter->setEnum($requestParameter->getEnum());
	}

	private function addNegotiationToSchemaMethod(SchemaMethod $schemaMethod, Negotiation $negotiation): void
	{
		$endpointNegotiation = $schemaMethod->addNegotiation($negotiation->getSuffix());

		$endpointNegotiation->setDefault($negotiation->isDefault());
		$endpointNegotiation->setRenderer($negotiation->getRenderer());
	}

	private function addResponseToSchemaMethod(SchemaMethod $schemaMethod, Response $response): void
	{
		$endpointResponse = $schemaMethod->addResponse($response->getCode(), $response->getDescription());

		$endpointResponse->setEntity($response->getEntity());
	}

}
