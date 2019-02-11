<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Negotiations
{

	/** @var Negotiation[] */
	private $negotiations = [];

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['value'])) {
			throw new AnnotationException('No @Negotiation given in @Negotiations');
		}

		$negotiations = $values['value'];
		if ($negotiations === []) {
			throw new AnnotationException('Empty @Negotiations given');
		}

		// Wrap single given request parameter into array
		if (!is_array($negotiations)) {
			$negotiations = [$negotiations];
		}

		$this->negotiations = $negotiations;
	}

	/**
	 * @return Negotiation[]
	 */
	public function getNegotiations(): array
	{
		return $this->negotiations;
	}

}
