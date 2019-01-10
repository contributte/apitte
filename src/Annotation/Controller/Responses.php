<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Responses
{

	/** @var Response[] */
	private $responses = [];

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @Responses given');
			}
			$this->responses = is_array($values['value']) ? $values['value'] : [$values['value']];
		} else {
			throw new AnnotationException('No @Response given in @Responses');
		}

		$takenCodes = [];
		/** @var Response $value */
		foreach ($values['value'] as $value) {
			if (!isset($takenCodes[$value->getCode()])) {
				$takenCodes[$value->getCode()] = $value;
			} else {
				throw new AnnotationException(sprintf(
					'Multiple @Response annotations with "code=%s" given. Each response must have unique code.',
					$value->getCode()
				));
			}
		}
	}

	/**
	 * @return Response[]
	 */
	public function getResponses(): array
	{
		return $this->responses;
	}

}
