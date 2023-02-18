<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping;

use ReflectionObject;

trait TReflectionProperties
{

	/** @var array<string, array<string, mixed>> */
	protected array $properties = [];

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public function getProperties(): array
	{
		if ($this->properties === []) {
			$properties = [];
			$rf = new ReflectionObject($this);
			$class = static::class;

			$defaultProperties = $rf->getDefaultProperties();
			foreach ($rf->getProperties() as $property) {
				// If property is not from the latest child, then skip it.
				if ($property->getDeclaringClass()->getName() !== $class) {
					continue;
				}

				// If property is not public, then skip it.
				if (!$property->isPublic()) {
					continue;
				}

				$name = $property->getName();
				$properties[$name] = [
					'name' => $name,
					'type' => $property->isInitialized($this) ? $property->getValue($this) : null,
					'defaultValue' => $defaultProperties[$name] ?? null,
				];
			}

			$this->properties = $properties;
		}

		return $this->properties;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$data = [];
		$properties = $this->getProperties();

		foreach ($properties as $property) {
			if (!isset($this->{$property['name']})) {
				continue;
			}

			$data[$property['name']] = $this->{$property['name']};
		}

		return $data;
	}

}
