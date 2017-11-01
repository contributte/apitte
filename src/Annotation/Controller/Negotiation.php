<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class Negotiation
{

	/** @var string */
	private $suffix;

	/** @var bool */
	private $default = FALSE;

	/** @var string */
	private $callback;

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['suffix'])) {
			throw new AnnotationException('Suffix is required at @Negotiation');
		}

		$this->suffix = $values['suffix'];

		if (isset($values['default'])) {
			$this->default = $values['default'];
		}

		if (isset($values['callback'])) {
			$this->callback = $values['callback'];
		}
	}

	/**
	 * @return string
	 */
	public function getSuffix()
	{
		return $this->suffix;
	}

	/**
	 * @return bool
	 */
	public function isDefault()
	{
		return $this->default;
	}

	/**
	 * @return string
	 */
	public function getCallback()
	{
		return $this->callback;
	}

}
