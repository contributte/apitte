<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;
use Nette\Utils\Arrays;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RequestParameter
{

	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var string */
	private $description;

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['name'])) {
			throw new AnnotationException('Name is required at @RequestParameter');
		}

		if (!isset($values['type']) && !isset($values['description'])) {
			throw new AnnotationException('Type or description is required at @RequestParameter');
		}

		$this->name = $values['name'];
		$this->type = Arrays::get($values, 'type', NULL);
		$this->description = Arrays::get($values, 'description', NULL);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

}
