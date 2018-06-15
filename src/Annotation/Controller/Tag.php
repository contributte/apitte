<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Nette\Utils\Arrays;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class Tag
{

	/** @var string */
	private $name;

	/** @var string|null */
	private $value;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value']) && !isset($values['name'])) {
			$values['name'] = $values['value'];
			unset($values['value']);
		}

		$this->name = $values['name'];
		$this->value = Arrays::get($values, 'value', null);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

}
