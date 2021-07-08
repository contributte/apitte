<?php declare(strict_types = 1);

namespace Apitte\Core\MetadataReader;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Koriym\Attributes\AttributeReader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * This reader reads BOTH annotations and attributes above one elements at the same time,
 * and merge them together.
 * Compared to @see DualReader that only prefers one or the other.
 */
final class AnnotationAttributeDualReader implements Reader
{

	/** @var AnnotationReader */
	private $annotationReader;

	/** @var AttributeReader */
	private $attributeReader;

	/** @var bool */
	private $php8;

	public function __construct(
		AnnotationReader $annotationReader,
		AttributeReader $attributeReader
	)
	{
		$this->annotationReader = $annotationReader;
		$this->attributeReader = $attributeReader;

		$this->php8 = PHP_VERSION_ID >= 80000;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMethodAnnotations(ReflectionMethod $method): array
	{
		$annotations = $this->annotationReader->getMethodAnnotations($method);
		if (! $this->php8) {
			return $annotations;
		}

		$attributes = $this->attributeReader->getMethodAnnotations($method);
		return array_merge($annotations, $attributes);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getClassAnnotations(ReflectionClass $class): array
	{
		$annotations = $this->annotationReader->getClassAnnotations($class);
		if (! $this->php8) {
			return $annotations;
		}

		$attributes = $this->attributeReader->getClassAnnotations($class);
		return array_merge($annotations, $attributes);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getClassAnnotation(ReflectionClass $class, $annotationName): ?object
	{
		$annotation = $this->annotationReader->getClassAnnotation($class, $annotationName);
		if (! $this->php8) {
			return $annotation;
		}

		return $this->attributeReader->getClassAnnotation($class, $annotationName);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMethodAnnotation(ReflectionMethod $method, $annotationName): ?object
	{
		$annotatoin = $this->annotationReader->getMethodAnnotation($method, $annotationName);
		if (! $this->php8) {
			return $annotatoin;
		}

		return $this->attributeReader->getMethodAnnotation($method, $annotationName);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPropertyAnnotations(ReflectionProperty $property): array
	{
		$annotations = $this->annotationReader->getPropertyAnnotations($property);
		if (!$this->php8) {
			return $annotations;
		}

		$attributes = $this->attributeReader->getPropertyAnnotations($property);
		return array_merge($annotations, $attributes);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPropertyAnnotation(ReflectionProperty $property, $annotationName): ?object
	{
		$annotation = $this->annotationReader->getPropertyAnnotation($property, $annotationName);
		if (! $this->php8) {
			return $annotation;
		}

		return $this->attributeReader->getPropertyAnnotation($property, $annotationName);
	}

}
