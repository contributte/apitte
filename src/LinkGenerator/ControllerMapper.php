<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

use Apitte\Core\Exception\Logical\InvalidControllerException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\UI\Controller\IController;
use ReflectionClass;

class ControllerMapper
{

	/** @var mixed[] of module => splitted mask */
	private $mapping = [
		'*' => ['', '*\\', '*Controller'],
	];

	/** @var string[] */
	private $classCache = [];

	/**
	 * Sets mapping as pairs [module => mask]
	 *
	 * @param mixed[] $mapping
	 * @return static
	 */
	public function setMapping(array $mapping): self
	{
		foreach ($mapping as $module => $mask) {
			if (is_string($mask)) {
				if (!preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#', $mask, $m)) {
					throw new InvalidStateException(sprintf('Invalid mapping mask "%s" for module "%s".', $mask, $module));
				}
				$this->mapping[$module] = [$m[1], $m[2] ?: '*Module\\', $m[3]];
			} elseif (is_array($mask) && count($mask) === 3) {
				$this->mapping[$module] = [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
			} else {
				throw new InvalidStateException(sprintf('Invalid mapping mask for module "%s".', $module));
			}
		}
		return $this;
	}

	private function formatControllerClass(string $controller): string
	{
		$parts = explode(':', $controller);
		$mapping = isset($parts[1], $this->mapping[$parts[0]])
			? $this->mapping[array_shift($parts)]
			: $this->mapping['*'];
		while ($part = array_shift($parts)) {
			$mapping[0] .= str_replace('*', $part, $mapping[$parts ? 1 : 2]);
		}
		return $mapping[0];
	}

	private function unformatControllerClass(string $class): ?string
	{
		foreach ($this->mapping as $module => $mapping) {
			$mapping = str_replace(['\\', '*'], ['\\\\', '(\w+)'], $mapping);
			$matchPattern = sprintf('#^\\\\?%s((?:%s)*)%s\\z#i', $mapping[0], $mapping[1], $mapping[2]);
			if (preg_match($matchPattern, $class, $matches)) {
				$replacePattern = sprintf('#%s#iA', $mapping[1]);
				return ($module === '*' ? '' : $module . ':')
					. preg_replace($replacePattern, '$1:', $matches[1]) . $matches[3];
			}
		}
		return null;
	}

	public function getControllerClass(string $name): string
	{
		if (isset($this->classCache[$name])) {
			return $this->classCache[$name];
		}

		if (!preg_match('#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*\z#', $name)) {
			throw new InvalidControllerException(sprintf('Controller name must be alphanumeric string, "%s" is invalid.', $name));
		}

		$class = $this->formatControllerClass($name);

		if (!class_exists($class)) {
			throw new InvalidControllerException(sprintf('Cannot load controller "%s", class "%s" was not found.', $name, $class));
		}

		$reflection = new ReflectionClass($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface(IController::class)) {
			throw new InvalidControllerException(sprintf('Cannot load controller "%s", class "%s" is not "%s" implementor.', $name, $class, IController::class));
		}

		if ($reflection->isAbstract()) {
			throw new InvalidControllerException(sprintf('Cannot load controller "%s", class "%s" is abstract.', $name, $class));
		}

		$this->classCache[$name] = $class;

		if ($name !== ($realName = $this->unformatControllerClass($class))) {
			trigger_error(sprintf('Case mismatch on controller name "%s", correct name is "%s".', $name, $realName), E_USER_WARNING);
		}

		return $class;
	}

}
