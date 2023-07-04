<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Server
{

	private string $url;

	private ?string $description = null;

	/** @var ServerVariable[] */
	private array $variables = [];

	public function __construct(string $url)
	{
		$this->url = $url;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Server
	{
		$server = new Server($data['url']);
		$server->setDescription($data['description'] ?? null);
		if (isset($data['variables'])) {
			foreach ($data['variables'] as $key => $variable) {
				$server->addVariable($key, ServerVariable::fromArray($variable));
			}
		}

		return $server;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['url'] = $this->url;

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		foreach ($this->variables as $variableKey => $variable) {
			$data['variables'][$variableKey] = $variable->toArray();
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function addVariable(string $key, ServerVariable $variable): void
	{
		$this->variables[$key] = $variable;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return ServerVariable[]
	 */
	public function getVariables(): array
	{
		return $this->variables;
	}

}
