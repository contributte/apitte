<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;
use ReflectionClass;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class Negotiation
{

	/** @var string */
	private $suffix;

	/** @var bool */
	private $default = false;

	/** @var string|null */
	private $renderer;

	/**
	 * @param mixed[] $values
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

		if (isset($values['renderer'])) {
			if (!class_exists($values['renderer'])) {
				throw new AnnotationException(sprintf('Renderer "%s" at @Negotiation does not exists', $values['renderer']));
			}

			$reflection = new ReflectionClass($values['renderer']);
			if (!$reflection->hasMethod('__invoke')) {
				throw new AnnotationException(sprintf('Renderer "%s" does not implement __invoke(ApiRequest $request, ApiResponse $response, array $context): ApiResponse', $values['renderer']));
			}

			$this->renderer = $values['renderer'];
		}
	}

	public function getSuffix(): string
	{
		return $this->suffix;
	}

	public function isDefault(): bool
	{
		return $this->default;
	}

	public function getRenderer(): ?string
	{
		return $this->renderer;
	}

}
