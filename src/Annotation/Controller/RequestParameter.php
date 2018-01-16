<?php

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Schema\EndpointParameter;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;
use Nette\Utils\Arrays;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class RequestParameter
{

	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var string */
	private $description;

	/** @var string */
	private $in = EndpointParameter::IN_PATH;

	/** @var bool */
	private $required = TRUE;

	/** @var bool */
	private $deprecated = FALSE;

	/** @var bool */
	private $allowEmpty = FALSE;

	/**
	 * @param mixed[] $values
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

		// @todo validation allowed values
		$this->in = Arrays::get($values, 'in', EndpointParameter::IN_PATH);
		$this->required = Arrays::get($values, 'required', TRUE);
		$this->deprecated = Arrays::get($values, 'deprecated', FALSE);
		$this->allowEmpty = Arrays::get($values, 'allowEmpty', FALSE);
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

	/**
	 * @return string
	 */
	public function getIn()
	{
		return $this->in;
	}

	/**
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @return bool
	 */
	public function isDeprecated()
	{
		return $this->deprecated;
	}

	/**
	 * @return bool
	 */
	public function isAllowEmpty()
	{
		return $this->allowEmpty;
	}

}
