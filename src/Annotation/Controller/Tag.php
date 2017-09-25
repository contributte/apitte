<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;
use Nette\Utils\Arrays;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Tag
{

	/** @var string */
	private $name;

	/** @var string */
	private $value;

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['name'])) {
			throw new AnnotationException('Name is required at @Tag');
		}

		$this->name = $values['name'];
		$this->value = Arrays::get($values, 'value', NULL);
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
	public function getValue()
	{
		return $this->value;
	}

}
