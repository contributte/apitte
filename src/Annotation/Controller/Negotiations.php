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
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @Negotiations given');
			}
			$this->negotiations = $values['value'];
		} else {
			throw new AnnotationException('No @Negotiations given');
		}

		$haveDefault = null;
		$takenSuffixes = [];
		/** @var Negotiation $negotiation */
		foreach ($values['value'] as $negotiation) {
			if ($negotiation->isDefault() === true) {
				if ($haveDefault !== null) {
					throw new AnnotationException('Multiple @Negotiation annotations with "default=true" given. Only one @Negotiation could be default.');
				}
				$haveDefault = $negotiation;
			}

			if (!isset($takenSuffixes[$negotiation->getSuffix()])) {
				$takenSuffixes[$negotiation->getSuffix()] = $negotiation;
			} else {
				throw new AnnotationException(sprintf('Multiple @Negotiation with "suffix=%s" given. Each @Negotiation must have unique suffix', $negotiation->getSuffix()));
			}
		}
	}

	/**
	 * @return Negotiation[]
	 */
	public function getNegotiations(): array
	{
		return $this->negotiations;
	}

}
