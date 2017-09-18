<?php

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Builder\SchemaController;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Nette\Reflection\ClassType;

final class DoctrineAnnotationLoader extends AnnotationLoader
{

	/** @var AnnotationReader */
	private $reader;

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

			// Check if a controller has @Controller annotation, otherwise, skip this controller
			if (!$class->hasAnnotation('Controller')) continue;

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
	 * @param SchemaController $controller
	 * @param ClassType $class
	 * @return void
	 */
	protected function parseControllerClassAnnotations(SchemaController $controller, ClassType $class)
	{
		// Read class annotations
		$annotations = $this->createReader()->getClassAnnotations($class);

		// Iterate over all class annotations
		foreach ($annotations as $annotation) {
			// Parse @RootPath
			if (get_class($annotation) == RootPath::class) {
				$controller->setRootPath($annotation->getPath());
				continue;
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
