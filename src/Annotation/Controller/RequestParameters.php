<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RequestParameters
{

	/** @var RequestParameter[] */
	private $parameters = [];

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @RequestParameters given');
			}
			$this->parameters = is_array($values['value']) ? $values['value'] : [$values['value']];
		} else {
			throw new AnnotationException('No @RequestParameter given in @RequestParameters');
		}

		$takenNames = [];
		/** @var RequestParameter $value */
		foreach ($values['value'] as $value) {
			if (!isset($takenNames[$value->getIn()][$value->getName()])) {
				$takenNames[$value->getIn()][$value->getName()] = $value;
			} else {
				throw new AnnotationException(sprintf(
					'Multiple @RequestParameter annotations with "name=%s" and "in=%s" given. Each parameter must have unique combination of location and name.',
					$value->getName(),
					$value->getIn()
				));
			}
		}
	}

	/**
	 * @return RequestParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

}
