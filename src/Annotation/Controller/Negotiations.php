<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Negotiations
{

	/** @var Negotiation[] */
	private $negotiations = [];

	/**
	 * @param Negotiation[]|Negotiation $negotiations
	 */
	public function __construct($negotiations)
	{
		if (empty($negotiations)) {
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
