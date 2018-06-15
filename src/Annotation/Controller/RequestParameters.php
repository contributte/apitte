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
			$this->parameters = $values['value'];
		} elseif (isset($values['parameters'])) {
			if (empty($values['parameters'])) {
				throw new AnnotationException('Empty @RequestParameters given');
			}
			$this->parameters = $values['parameters'];
		} else {
			throw new AnnotationException('No @RequestParameters given');
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
