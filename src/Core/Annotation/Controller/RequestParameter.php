<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Schema\EndpointParameter;
use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RequestParameter
{

	/**
	 * @param list<string|int>|null $enum
	 */
	public function __construct(
		private readonly string $name,
		private readonly string $type,
		private readonly string $in = EndpointParameter::IN_PATH,
		private readonly bool $required = true,
		private readonly bool $allowEmpty = false,
		private readonly bool $deprecated = false,
		private readonly ?string $description = null,
		private readonly ?array $enum = null
	)
	{
		if ($name === '') {
			throw new AnnotationException('Empty @RequestParameter name given');
		}

		if ($type === '') {
			throw new AnnotationException('Empty @RequestParameter type given');
		}

		if (!in_array($in, EndpointParameter::IN, true)) {
			throw new AnnotationException('Invalid @RequestParameter in given');
		}
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getIn(): string
	{
		return $this->in;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}

	public function isAllowEmpty(): bool
	{
		return $this->allowEmpty;
	}

	/**
	 * @return list<string|int>|null
	 */
	public function getEnum(): ?array
	{
		return $this->enum;
	}

}
