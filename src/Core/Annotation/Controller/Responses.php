<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
class Responses
{

	/** @var Response[] */
	private readonly array $responses;

	/**
	 * @param Response[]|Response $responses
	 */
	public function __construct(array|Response $responses)
	{
		if (empty($responses)) {
			throw new AnnotationException('Empty @Responses given');
		}

		// Wrap single given response into array
		if (!is_array($responses)) {
			$responses = [$responses];
		}

		$takenCodes = [];

		/** @var Response $response */
		foreach ($responses as $response) {
			if (!isset($takenCodes[$response->getCode()])) {
				$takenCodes[$response->getCode()] = $response;
			} else {
				throw new AnnotationException(sprintf(
					'Multiple @Response annotations with "code=%s" given. Each response must have unique code.',
					$response->getCode()
				));
			}
		}

		$this->responses = $responses;
	}

	/**
	 * @return Response[]
	 */
	public function getResponses(): array
	{
		return $this->responses;
	}

}
