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
		if (!isset($values['value'])) {
			throw new AnnotationException('No @RequestParameter given in @RequestParameters');
		}

		$parameters = $values['value'];
		if ($parameters === []) {
			throw new AnnotationException('Empty @RequestParameters given');
		}

		// Wrap single given request parameter into array
		if (!is_array($parameters)) {
			$parameters = [$parameters];
		}

		$takenNames = [];
		/** @var RequestParameter $parameter */
		foreach ($parameters as $parameter) {
			if (!isset($takenNames[$parameter->getIn()][$parameter->getName()])) {
				$takenNames[$parameter->getIn()][$parameter->getName()] = $parameter;
			} else {
				throw new AnnotationException(sprintf(
					'Multiple @RequestParameter annotations with "name=%s" and "in=%s" given. Each parameter must have unique combination of location and name.',
					$parameter->getName(),
					$parameter->getIn()
				));
			}
		}

		$this->parameters = $parameters;
	}

	/**
	 * @return RequestParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

}
