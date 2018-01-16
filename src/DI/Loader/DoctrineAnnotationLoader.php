<?php

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Annotation\Controller\Controller as ControllerAnnotation;
use Apitte\Core\Annotation\Controller\ControllerId;
use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\GroupId;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\Annotation\Controller\Id;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Negotiations;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestMapper;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\ResponseMapper;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Nette\Reflection\ClassType;
use Nette\Utils\Reflection;

final class DoctrineAnnotationLoader extends AbstractContainerLoader
{

	/** @var AnnotationReader */
	private $reader;

	/** @var array */
	private $meta = [
		'services' => [],
	];

	/**
	 * @param SchemaBuilder $builder
	 * @return SchemaBuilder
	 */
	public function load(SchemaBuilder $builder)
	{
		// Find all controllers by type (interface, annotation)
		$controllers = $this->findControllers();

		// Add controllers as dependencies to DIC
		$this->addDependencies($controllers);

		// Iterate over all controllers
		foreach ($controllers as $def) {
			// Analyse all parent classes
			$class = $this->analyseClass($def->getClass());

			// Check if a controller or his abstract has @Controller annotation,
			// otherwise, skip this controller
			if (!$this->acceptController($class)) continue;

			// Create scheme endpoint
			$schemeController = $builder->addController($def->getClass());

			// Parse @Controller, @ControllerPath, @ControllerId
			$this->parseControllerClassAnnotations($schemeController, $class);

			// Parse @Method, @Path
			$this->parseControllerMethodsAnnotations($schemeController, $class);
		}

		return $builder;
	}

	/**
	 * @param string $class
	 * @return ClassType
	 */
	protected function analyseClass($class)
	{
		// Analyse only new-ones
		if (isset($this->meta['services'][$class])) {
			return $this->meta['services'][$class]['reflection'];
		}

		// Create reflection
		$classRef = ClassType::from($class);

		// Index controller as service
		$this->meta['services'][$class] = [
			'reflection' => $classRef,
			'parents' => [],
		];

		// Get all parents
		$parents = class_parents($class);
		$reflections = [];

		// Iterate over all parents and analyse them
		foreach ((array) $parents as $parentClass) {
			// Stop multiple analysing
			if (isset($this->meta['services'][$parentClass])) {
				// Just reference it in reflections
				$reflections[$parentClass] = $this->meta['services'][$parentClass]['reflection'];
				continue;
			}

			// Create reflection for parent class
			$parentClassRf = ClassType::from($parentClass);
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
	 * @param ClassType $class
	 * @return bool
	 */
	protected function acceptController(ClassType $class)
	{
		// Has class annotation @Controller?
		if ($this->getReader()->getClassAnnotation($class, ControllerAnnotation::class)) return TRUE;

		// Has any of parent classes annotation @Controller?
		$parents = $this->meta['services'][$class->getName()]['parents'];

		/** @var ClassType $parentClass */
		foreach ($parents as $parentClass) {
			if ($this->getReader()->getClassAnnotation($parentClass, ControllerAnnotation::class)) return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param Controller $controller
	 * @param ClassType $class
	 * @return void
	 */
	protected function parseControllerClassAnnotations(Controller $controller, ClassType $class)
	{
		// Read class annotations
		$annotations = $this->getReader()->getClassAnnotations($class);

		// Iterate over all class annotations in controller
		foreach ($annotations as $annotation) {
			// Parse @ControllerPath =======================
			if (get_class($annotation) == ControllerPath::class) {
				/** @var ControllerPath $annotation */
				$controller->setPath($annotation->getPath());
				continue;
			}

			// Parse @ControllerId =========================
			if (get_class($annotation) == ControllerId::class) {
				/** @var ControllerId $annotation */
				$controller->setId($annotation->getName());
			}

			// Parse @Tag ==================================
			if (get_class($annotation) == Tag::class) {
				/** @var Tag $annotation */
				$controller->addTag($annotation->getName(), $annotation->getValue());
			}

			// Parse @GroupId ==============================
			if (get_class($annotation) == GroupId::class) {
				throw new InvalidStateException(sprintf('Annotation @GroupId cannot be on non-abstract "%s"', $class->getName()));
			}

			// Parse @GroupPath ============================
			if (get_class($annotation) == GroupPath::class) {
				throw new InvalidStateException(sprintf('Annotation @GroupPath cannot be on non-abstract "%s"', $class->getName()));
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
				if (get_class($annotation) == GroupId::class) {
					/** @var GroupId $annotation */
					$controller->addGroupId($annotation->getName());
				}

				// Parse @GroupPath ========================
				if (get_class($annotation) == GroupPath::class) {
					/** @var GroupPath $annotation */
					$controller->addGroupPath($annotation->getPath());
				}

				// Parse @Tag ==============================
				if (get_class($annotation) == Tag::class) {
					/** @var Tag $annotation */
					$controller->addTag($annotation->getName(), $annotation->getValue());
				}
			}
		}
	}

	/**
	 * @param Controller $controller
	 * @param ClassType $class
	 * @return void
	 */
	protected function parseControllerMethodsAnnotations(Controller $controller, ClassType $class)
	{
		// Iterate over all methods in class
		foreach ($class->getMethods() as $method) {
			// Skip protected/private methods
			if (!$method->isPublic()) continue;

			// Read method annotations
			$annotations = $this->getReader()->getMethodAnnotations($method);

			// Skip if method has no @Path/@Method annotations
			if (count($annotations) <= 0) continue;

			// Append method to scheme
			$schemaMethod = $controller->addMethod($method->getName());
			$schemaMethod->setDescription($method->getDescription());

			// Iterate over all method annotations
			foreach ($annotations as $annotation) {
				// Parse @Path =============================
				if (get_class($annotation) === Path::class) {
					/** @var Path $annotation */
					$schemaMethod->setPath($annotation->getPath());
					continue;
				}

				// Parse @Method ===========================
				if (get_class($annotation) === Method::class) {
					/** @var Method $annotation */
					$schemaMethod->addMethods($annotation->getMethods());
					continue;
				}

				// Parse @Id ===============================
				if (get_class($annotation) === Id::class) {
					/** @var Id $annotation */
					$schemaMethod->setId($annotation->getName());
					continue;
				}

				// Parse @RequestParameters ================
				if (get_class($annotation) === RequestParameters::class) {
					/** @var RequestParameters $annotation */
					foreach ($annotation->getParameters() as $p) {
						$parameter = $schemaMethod->addParameter($p->getName());
						$parameter->setType($p->getType());
						$parameter->setDescription($p->getDescription());
						$parameter->setIn($p->getIn());
						$parameter->setRequired($p->isRequired());
						$parameter->setDeprecated($p->isDeprecated());
						$parameter->setAllowEmpty($p->isAllowEmpty());
					}
					continue;
				}

				// Parse @Negotiations =====================
				if (get_class($annotation) === Negotiations::class) {
					/** @var Negotiations $annotation */
					foreach ($annotation->getNegotations() as $n) {
						$negotiation = $schemaMethod->addNegotiation();
						$negotiation->setSuffix($n->getSuffix());
						$negotiation->setDefault($n->isDefault());
						$negotiation->setRenderer($n->getRenderer());
					}
					continue;
				}

				// Parse @RequestMapper ====================
				if (get_class($annotation) === RequestMapper::class) {
					/** @var RequestMapper $annotation */
					$schemaMethod->setRequestMapper([
						'entity' => $annotation->getEntity(),
						'validation' => $annotation->isValidation(),
					]);
					continue;
				}

				// Parse @ResponseMapper ===================
				if (get_class($annotation) === ResponseMapper::class) {
					/** @var ResponseMapper $annotation */
					$schemaMethod->setResponseMapper([
						'entity' => $annotation->getEntity(),
					]);
					continue;
				}
			}

			// Parse method typed parameters
			foreach ($method->getParameters() as $parameter) {
				$type = Reflection::getParameterType($parameter);
				$schemaMethod->addArgument($parameter->getName(), $type);
			}
		}
	}

	/*
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return AnnotationReader
	 */
	private function getReader()
	{
		if (!$this->reader) {
			AnnotationRegistry::registerLoader('class_exists');
			$this->reader = new AnnotationReader();
		}

		return $this->reader;
	}

}
