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

	private string $name;

	private string $type;

	private ?string $description;

	private string $in;

	private bool $required;

	private bool $deprecated;

	private bool $allowEmpty;

	/** @var list<string|int>|null */
	private ?array $enum;

	/**
	 * @param list<string|int>|null $enum
	 */
	public function __construct(
		string $name,
		string $type,
		string $in = EndpointParameter::IN_PATH,
		bool $required = true,
		bool $allowEmpty = false,
		bool $deprecated = false,
		?string $description = null,
		?array $enum = null
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

		$this->name = $name;
		$this->type = $type;
		$this->required = $required;
		$this->allowEmpty = $allowEmpty;
		$this->deprecated = $deprecated;
		$this->description = $description;
		$this->in = $in;
		$this->enum = $enum;
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
