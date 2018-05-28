<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;
use Nette\Utils\Arrays;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
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
		if (isset($values['value']) && !isset($values['name'])) {
			$values['name'] = $values['value'];
			unset($values['value']);
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
