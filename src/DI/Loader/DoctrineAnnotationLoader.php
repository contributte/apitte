<?php

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Annotation\Controller\Group;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Builder\SchemaController;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Nette\Reflection\ClassType;
use Nette\Utils\Reflection;

final class DoctrineAnnotationLoader extends AnnotationLoader
{

	/** @var AnnotationReader */
	private $reader;

	/** @var array */
	private $meta = [
		'services' => [],
	];

	/**
	 * @return SchemaBuilder
	 */
	public function load()
	{
		$schemeBuilder = new SchemaBuilder();

		// Find all controllers by type (interface, annotation)
		$controllers = $this->findControllers();

		// Add controllers as dependencies to DIC
		$this->addDependencies($controllers);

		// Iterate over all controllers
		foreach ($controllers as $def) {
			// Create reflection
			$class = ClassType::from($def->getClass());

			// Index controller as service
			$this->meta['services'][$def->getClass()] = [
				'reflection' => $class,
				'parents' => [],
			];

			// Analyse all parent classes
			$this->analyseClassTree($class);

			// Check if a controller or his abstract has @Controller annotation,
			// otherwise, skip this controller
			if (!$this->acceptController($class)) continue;

			// Create scheme endpoint
			$schemeController = $schemeBuilder->addController($def->getClass());

			// Parse @Controller, @RootPath
			$this->parseControllerClassAnnotations($schemeController, $class);

			// Parse @Method, @Path
			$this->parseControllerMethodsAnnotations($schemeController, $class);
		}

		return $schemeBuilder;
	}

	/**
	 * @param ClassType $class
	 * @return void
	 */
	protected function analyseClassTree(ClassType $class)
	{
		// Geta all parents
		$parents = class_parents($class->getName());
		$reflections = [];

		// Iterate over all parents and analyse thems
		foreach ((array) $parents as $parentClass) {
			// Stop multiple analysing
			if (isset($this->meta['services'][$parentClass])) {
				// Just reference it in reflections
				$reflections[$parentClass] = $this->meta['services'][$parentClass]['reflection'];
				continue;
			}

			// Create reflection for parent class
			$rf = ClassType::from($parentClass);
			$reflections[$parentClass] = $rf;

			// Index service
			$this->meta['services'][$parentClass] = [
				'reflection' => $rf,
				'parents' => [],
			];

			// Analyse parent (recursive)
			$this->analyseClassTree($rf);
		}

		// Append all parents to this service
		$this->meta['services'][$class->getName()]['parents'] = $reflections;
	}

	/**
	 * @param ClassType $class
	 * @return bool
	 */
	protected function acceptController(ClassType $class)
	{
		// Has class annotation @Controller?
		if ($class->hasAnnotation('Controller')) return TRUE;

		// Has any of parent classes annotation @Controller?
		$parents = $this->meta['services'][$class->getName()]['parents'];

		/** @var ClassType $parentClass */
		foreach ($parents as $parentClass) {
			if ($parentClass->hasAnnotation('Controller')) return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param SchemaController $controller
	 * @param ClassType $class
	 * @return void
	 */
	protected function parseControllerClassAnnotations(SchemaController $controller, ClassType $class)
	{
		// Read class annotations
		$annotations = $this->createReader()->getClassAnnotations($class);

		// Iterate over all class annotations in controller
		foreach ($annotations as $annotation) {
			// Parse @RootPath
			if (get_class($annotation) == RootPath::class) {
				$controller->setRootPath($annotation->getPath());
				continue;
			}

			// Parse @Group
			if (get_class($annotation) == Group::class) {
				throw new InvalidStateException(sprintf('Annotation @Group cannot be on non-abstract "%s"', $class->getName()));
			}

			// Parse @GroupPath
			if (get_class($annotation) == GroupPath::class) {
				throw new InvalidStateException(sprintf('Annotation @GroupPath cannot be on non-abstract "%s"', $class->getName()));
			}
		}

		// Reverse order
		$reversed = array_reverse($this->meta['services'][$class->getName()]['parents']);

		// Iterate over all class annotations in controller's parents
		foreach ($reversed as $parent) {
			// Read parent class annotations
			$parentAnnotations = $this->createReader()->getClassAnnotations($parent);

			// Iterate over all parent class annotations
			foreach ($parentAnnotations as $annotation) {
				// Parse @Group
				if (get_class($annotation) == Group::class) {
					$controller->setGroup($annotation->getName());
				}

				// Parse @GroupPath
				if (get_class($annotation) == GroupPath::class) {
					$controller->addGroupPath($annotation->getPath());
				}
			}
		}
	}

	/**
	 * @param SchemaController $controller
	 * @param ClassType $class
	 * @return void
	 */
	protected function parseControllerMethodsAnnotations(SchemaController $controller, ClassType $class)
	{
		// Iterate over all methods in class
		foreach ($class->getMethods() as $method) {
			// Skip protected/private methods
			if (!$method->isPublic()) continue;

			// Read method annotations
			$annotations = $this->createReader()->getMethodAnnotations($method);

			// Skip if method has no @Path/@Method annotations
			if (count($annotations) <= 0) continue;

			// Append method to scheme
			$schemaMethod = $controller->addMethod($method->getName());

			// Iterate over all method annotations
			foreach ($annotations as $annotation) {
				// Parse @Path =========================
				if (get_class($annotation) === Path::class) {
					$schemaMethod->setPath($annotation->getPath());
					continue;
				}

				// Parse @Method =======================
				if (get_class($annotation) === Method::class) {
					$schemaMethod->appendMethods($annotation->getMethods());
					continue;
				}
			}

			// Parse method parameters
			foreach ($method->getParameters() as $parameter) {
				$type = Reflection::getParameterType($parameter);
				$schemaMethod->appendArgument($parameter->getName(), $type);
			}
		}
	}

	/*
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return AnnotationReader
	 */
	private function createReader()
	{
		if (!$this->reader) {
			AnnotationRegistry::registerLoader('class_exists');
			$this->reader = new AnnotationReader();
		}

		return $this->reader;
	}

}
