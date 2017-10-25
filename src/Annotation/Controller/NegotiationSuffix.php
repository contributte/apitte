<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class NegotiationSuffix extends Negotiation
{

	/** @var string */
	private $suffix;

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		parent::__construct(['type' => 'suffix']);

		if (!isset($values['suffix'])) {
			throw new AnnotationException('Suffix is required at @NegotiationSuffix');
		}

		$this->suffix = $values['suffix'];
	}

	/**
	 * @return string
	 */
	public function getSuffix()
	{
		return $this->suffix;
	}

}
