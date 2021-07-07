<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Annotation\Controller\Id;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Negotiation;
use Apitte\Core\Annotation\Controller\Negotiations;
use Apitte\Core\Annotation\Controller\OpenApi;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\DI\LoaderFactory\DualReaderFactory;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method as SchemaMethod;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\UI\Controller\IController;
use Doctrine\Common\Annotations\Reader;
use Nette\Neon\Neon;
use ReflectionClass;
use ReflectionMethod;

final class DoctrineAnnotationLoader extends AbstractContainerLoader
{

	/** @var Reader|null */
	private $reader;

	/** @var mixed[] */
	private $meta = [
		'services' => [],
	];

	public function load(SchemaBuilder $builder): SchemaBuilder
	{
		// Find all controllers by type (interface, annotation)
		$controllers = $this->findControllers();

		// Iterate over all controllers
		foreach ($controllers as $def) {
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

			// Parse @Path, @ControllerId
			$this->parseControllerClassAnnotations($schemeController, $class);

			// Parse @Method, @Path
			$this->parseControllerMethodsAnnotations($schemeController, $class);
		}

		return $builder;
	}

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
		/** @var string[] $parents */
		$parents = class_parents($class);
		$reflections = [];

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

	protected function acceptController(ReflectionClass $class): bool
	{
		return is_subclass_of($class->getName(), IController::class);
	}

	protected function parseControllerClassAnnotations(Controller $controller, ReflectionClass $class): void
	{
		// Read class annotations
		$annotations = $this->getReader()->getClassAnnotations($class);

		// Iterate over all class annotations in controller
		foreach ($annotations as $annotation) {
			// Parse @Path =======================
			if ($annotation instanceof Path) {
				$controller->setPath($annotation->getPath());
				continue;
			}

			// Parse @ControllerId =========================
			if ($annotation instanceof Id) {
				$controller->setId($annotation->getName());
			}

			// Parse @Tag ==================================
			if ($annotation instanceof Tag) {
				$controller->addTag($annotation->getName(), $annotation->getValue());
			}

			// Parse @OpenApi ============================
			if ($annotation instanceof OpenApi) {
				$controller->setOpenApi(Neon::decode($annotation->getData()) ?? []);
				continue;
			}
		}

		// Reverse order
		$reversed = array_reverse($this->meta['services'][$class->getName()]['parents']);

		// Iterate over all class annotations in controller's parents
		foreach ($reversed as $parent) {
			// Read parent class annotations
			$parentAnnotations = $this->getReader()->getClassAnnotations($parent);

			// Iterate over all parent class annotations
			foreach ($parentAnnotations as $annotation) {
				// Parse @GroupId ==========================
				if ($annotation instanceof Id) {
					$controller->addGroupId($annotation->getName());
				}

				// Parse @Path ========================
				if ($annotation instanceof Path) {
					$controller->addGroupPath($annotation->getPath());
				}

				// Parse @Tag ==============================
				if ($annotation instanceof Tag) {
					$controller->addTag($annotation->getName(), $annotation->getValue());
				}
			}
		}
	}

	protected function parseControllerMethodsAnnotations(Controller $controller, ReflectionClass $class): void
	{
		// Iterate over all methods in class
		foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			// Read method annotations
			$annotations = $this->getReader()->getMethodAnnotations($method);

			// Skip if method has no @Path/@Method annotations
			if (count($annotations) <= 0) {
				continue;
			}

			// Append method to scheme
			$schemaMethod = $controller->addMethod($method->getName());

			// Iterate over all method annotations
			foreach ($annotations as $annotation) {
				// Parse @Path =============================
				if ($annotation instanceof Path) {
					$schemaMethod->setPath($annotation->getPath());
					continue;
				}

				// Parse @Method ===========================
				if ($annotation instanceof Method) {
					$schemaMethod->addHttpMethods($annotation->getMethods());
					continue;
				}

				// Parse @Tag ==============================
				if ($annotation instanceof Tag) {
					$schemaMethod->addTag($annotation->getName(), $annotation->getValue());
					continue;
				}

				// Parse @Id ===============================
				if ($annotation instanceof Id) {
					$schemaMethod->setId($annotation->getName());
					continue;
				}

				// Parse @RequestParameters ================
				if ($annotation instanceof RequestParameters) {
					foreach ($annotation->getParameters() as $parameter) {
						$this->addEndpointParameterToSchemaMethod($schemaMethod, $parameter);
					}

					continue;
				}

				// Parse #[RequestParameter] ================
				if ($annotation instanceof RequestParameter) {
					$this->addEndpointParameterToSchemaMethod($schemaMethod, $annotation);

					continue;
				}

				// Parse @Responses ================
				if ($annotation instanceof Responses) {
					foreach ($annotation->getResponses() as $r) {
						$response = $schemaMethod->addResponse($r->getCode(), $r->getDescription());
						$response->setEntity($r->getEntity());
					}

					continue;
				}

				// Parse #[Response] attribute
				if ($annotation instanceof Response) {
					$response = $schemaMethod->addResponse($annotation->getCode(), $annotation->getDescription());
					$response->setEntity($annotation->getEntity());
					continue;
				}

				// Parse @Request ================
				if ($annotation instanceof RequestBody) {
					$requestBody = new EndpointRequestBody();
					$requestBody->setDescription($annotation->getDescription());
					$requestBody->setEntity($annotation->getEntity());
					$requestBody->setRequired($annotation->isRequired());
					$requestBody->setValidation($annotation->isValidation());
					$schemaMethod->setRequestBody($requestBody);
					continue;
				}

				// Parse @OpenApi ================
				if ($annotation instanceof OpenApi) {
					$schemaMethod->setOpenApi(Neon::decode($annotation->getData()) ?? []);
					continue;
				}

				// Parse @Negotiations =====================
				if ($annotation instanceof Negotiations) {
					foreach ($annotation->getNegotiations() as $negotiation) {
						$this->addNegotiationToSchemaMethod($schemaMethod, $negotiation);
					}
				}

				// Parse #[Negotiation] =====================
				if ($annotation instanceof Negotiation) {
					$this->addNegotiationToSchemaMethod($schemaMethod, $annotation);
				}
			}
		}
	}

	private function getReader(): Reader
	{
		if (!$this->reader) {
			$dualReaderFactory = new DualReaderFactory();
			$this->reader = $dualReaderFactory->create();
		}

		return $this->reader;
	}

	private function addEndpointParameterToSchemaMethod(SchemaMethod $schemaMethod, RequestParameter $requestParameter): void
	{
		$parameter = $schemaMethod->addParameter($requestParameter->getName(), $requestParameter->getType());

		$parameter->setDescription($requestParameter->getDescription());
		$parameter->setIn($requestParameter->getIn());
		$parameter->setRequired($requestParameter->isRequired());
		$parameter->setDeprecated($requestParameter->isDeprecated());
		$parameter->setAllowEmpty($requestParameter->isAllowEmpty());
	}

	private function addNegotiationToSchemaMethod(SchemaMethod $schemaMethod, Negotiation $negotiation): void
	{
		$endpointNegotiation = $schemaMethod->addNegotiation($negotiation->getSuffix());
		$endpointNegotiation->setDefault($negotiation->isDefault());
		$endpointNegotiation->setRenderer($negotiation->getRenderer());
	}

}
