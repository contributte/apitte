<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

final class Controller
{

	private string $class;

	/** @var Method[] */
	private array $methods = [];

	private ?string $id = null;

	private string $path = '';

	/** @var string[] */
	private array $groupIds = [];

	/** @var string[] */
	private array $groupPaths = [];

	/** @var mixed[] */
	private array $tags = [];

	/** @var mixed[] */
	private array $openApi = [];

	public function __construct(string $class)
	{
		$this->class = $class;
	}

	public function getClass(): string
	{
		return $this->class;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function setPath(string $path): void
	{
		$this->path = $path;
	}

	/**
	 * @return Method[]
	 */
	public function getMethods(): array
	{
		return $this->methods;
	}

	public function addMethod(string $name): Method
	{
		$method = new Method($name);
		$this->methods[$name] = $method;

		return $method;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): void
	{
		$this->id = $id;
	}

	/**
	 * @return mixed[]
	 */
	public function getGroupIds(): array
	{
		return $this->groupIds;
	}

	/**
	 * @param string[] $ids
	 */
	public function setGroupIds(array $ids): void
	{
		$this->groupIds = $ids;
	}

	public function addGroupId(string $id): void
	{
		$this->groupIds[] = $id;
	}

	/**
	 * @return string[]
	 */
	public function getGroupPaths(): array
	{
		return $this->groupPaths;
	}

	/**
	 * @param string[] $groupPaths
	 */
	public function setGroupPaths(array $groupPaths): void
	{
		$this->groupPaths = $groupPaths;
	}

	public function addGroupPath(string $path): void
	{
		$this->groupPaths[] = $path;
	}

	/**
	 * @return mixed[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	/**
	 * @param mixed $value
	 */
	public function addTag(string $name, $value = null): void
	{
		$this->tags[$name] = $value;
	}

	/**
	 * @param mixed[] $tags
	 */
	public function addTags(array $tags): void
	{
		foreach ($tags as $name => $value) {
			$this->addTag($name, $value);
		}
	}

	/**
	 * @param mixed[] $openApi
	 */
	public function setOpenApi(array $openApi): void
	{
		$this->openApi = $openApi;
	}

	/**
	 * @return mixed[]
	 */
	public function getOpenApi(): array
	{
		return $this->openApi;
	}

}
