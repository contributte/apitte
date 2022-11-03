<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class OpenApi
{

	private string $openapi;

	private Info $info;

	/** @var Server[] */
	private array $servers = [];

	private Paths $paths;

	private ?Components $components = null;

	/** @var SecurityRequirement[] */
	private array $security = [];

	/** @var Tag[] */
	private array $tags = [];

	private ?ExternalDocumentation $externalDocs = null;

	public function __construct(string $openapi, Info $info, Paths $paths)
	{
		$this->openapi = $openapi;
		$this->info = $info;
		$this->paths = $paths;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['openapi'] = $this->openapi;
		$data['info'] = $this->info->toArray();

		foreach ($this->servers as $server) {
			$data['servers'][] = $server->toArray();
		}

		$data['paths'] = $this->paths->toArray();

		if ($this->components !== null) {
			$data['components'] = $this->components->toArray();
		}

		foreach ($this->security as $requirement) {
			$data['security'][] = $requirement->toArray();
		}

		foreach ($this->tags as $tag) {
			$data['tags'][] = $tag->toArray();
		}

		if ($this->externalDocs !== null) {
			$data['externalDocs'] = $this->externalDocs->toArray();
		}

		return $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): OpenApi
	{
		$openApi = new OpenApi(
			$data['openapi'],
			Info::fromArray($data['info']),
			Paths::fromArray($data['paths'])
		);
		if (isset($data['servers'])) {
			foreach ($data['servers'] as $serverData) {
				$openApi->addServer(Server::fromArray($serverData));
			}
		}

		if (isset($data['components'])) {
			$openApi->setComponents(Components::fromArray($data['components']));
		}

		if (isset($data['tags'])) {
			foreach ($data['tags'] as $tagData) {
				$openApi->addTag(Tag::fromArray($tagData));
			}
		}

		if (isset($data['externalDocs'])) {
			$openApi->externalDocs = ExternalDocumentation::fromArray($data['externalDocs']);
		}

		if (isset($data['security'])) {
			foreach ($data['security'] as $security) {
				$openApi->addSecurityRequirement(SecurityRequirement::fromArray($security));
			}
		}

		return $openApi;
	}

	public function addTag(Tag $tag): void
	{
		$this->tags[] = $tag;
	}

	public function addServer(Server $server): void
	{
		$this->servers[] = $server;
	}

	public function setComponents(?Components $components): void
	{
		$this->components = $components;
	}

	public function setExternalDocs(?ExternalDocumentation $externalDocs): void
	{
		$this->externalDocs = $externalDocs;
	}

	public function addSecurityRequirement(SecurityRequirement $security): void
	{
		$this->security[] = $security;
	}

}
