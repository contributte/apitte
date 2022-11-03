<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

use Apitte\Core\Exception\Logical\InvalidArgumentException;

class SecurityScheme
{

	public const
		TYPE_API_KEY = 'apiKey',
		TYPE_HTTP = 'http',
		TYPE_OAUTH2 = 'oauth2',
		TYPE_OPEN_ID_CONNECT = 'openIdConnect';

	public const TYPES = [
		self::TYPE_API_KEY,
		self::TYPE_HTTP,
		self::TYPE_OAUTH2,
		self::TYPE_OPEN_ID_CONNECT,
	];

	public const
		IN_COOKIE = 'cookie',
		IN_HEADER = 'header',
		IN_QUERY = 'query';

	public const INS = [
		self::IN_COOKIE,
		self::IN_HEADER,
		self::IN_QUERY,
	];

	public const FLOWS = [
		'implicit',
		'password',
		'clientCredentials',
		'authorizationCode',
	];

	private string $type;

	private ?string $name = null;

	private ?string $description = null;

	private ?string $in = null;

	private ?string $scheme = null;

	private ?string $bearerFormat = null;

	/** @var array<string, OAuthFlow> */
	private array $flows = [];

	private ?string $openIdConnectUrl = null;

	public function __construct(string $type)
	{
		$this->setType($type);
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['type'] = $this->type;
		if ($this->name !== null) {
			$data['name'] = $this->name;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->in !== null) {
			$data['in'] = $this->in;
		}

		if ($this->scheme !== null) {
			$data['scheme'] = $this->scheme;
		}

		if ($this->bearerFormat !== null) {
			$data['bearerFormat'] = $this->bearerFormat;
		}

		if ($this->flows !== []) {
			$data['flows'] = array_map(static fn(OAuthFlow $flow): array => $flow->toArray(), $this->flows);
		}

		if ($this->openIdConnectUrl !== null) {
			$data['openIdConnectUrl'] = $this->openIdConnectUrl;
		}

		return $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): SecurityScheme
	{
		$type = $data['type'];
		$securityScheme = new SecurityScheme($type);
		$securityScheme->setName($data['name'] ?? null);
		$securityScheme->setDescription($data['description'] ?? null);
		$securityScheme->setIn($data['in'] ?? null);
		$securityScheme->setScheme($data['scheme'] ?? null);
		$securityScheme->setBearerFormat($data['bearerFormat'] ?? null);
		$securityScheme->setFlows(array_map(static fn(array $flow): OAuthFlow => OAuthFlow::fromArray($flow), $data['flows'] ?? []));
		$securityScheme->setOpenIdConnectUrl($data['openIdConnectUrl'] ?? null);
		return $securityScheme;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		if (!in_array($type, self::TYPES, true)) {
			throw new InvalidArgumentException(sprintf(
				'Invalid value "%s" for attribute "type" given. It must be one of "%s".',
				$type,
				implode(', ', self::TYPES)
			));
		}

		$this->type = $type;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): void
	{
		if ($this->type === self::TYPE_API_KEY && $name === null) {
			throw new InvalidArgumentException('Attribute "name" is required for type "apiKey".');
		}

		$this->name = $name;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getIn(): ?string
	{
		return $this->in;
	}

	public function setIn(?string $in): void
	{
		if ($this->type === self::TYPE_API_KEY && $in === null) {
			throw new InvalidArgumentException('Attribute "in" is required for type "apiKey".');
		}

		if ($in === null || in_array($in, self::INS, true)) {
			$this->in = $in;
			return;
		}

		throw new InvalidArgumentException(sprintf(
			'Invalid value "%s" for attribute "in" given. It must be one of "%s".',
			$in,
			implode(', ', self::INS)
		));
	}

	public function getScheme(): ?string
	{
		return $this->scheme;
	}

	public function setScheme(?string $scheme): void
	{
		if ($this->type === self::TYPE_HTTP && $scheme === null) {
			throw new InvalidArgumentException('Attribute "scheme" is required for type "http".');
		}

		$this->scheme = $scheme;
	}

	public function getBearerFormat(): ?string
	{
		return $this->bearerFormat;
	}

	public function setBearerFormat(?string $bearerFormat): void
	{
		if ($this->type === self::TYPE_HTTP && $this->scheme === 'bearer' && $bearerFormat === null) {
			throw new InvalidArgumentException('Attribute "bearerFormat" is required for type "http" and scheme "bearer".');
		}

		$this->bearerFormat = $bearerFormat;
	}

	/**
	 * @return array<string, OAuthFlow>
	 */
	public function getFlows(): array
	{
		return $this->flows;
	}

	/**
	 * @param array<string, OAuthFlow> $flows
	 */
	public function setFlows(array $flows): void
	{
		if ($this->type === self::TYPE_OAUTH2 && $flows === []) {
			throw new InvalidArgumentException('Attribute "flows" is required for type "oauth2".');
		}

		if ($this->type === self::TYPE_OAUTH2) {
			foreach (self::FLOWS as $flow) {
				if (!array_key_exists($flow, $flows)) {
					throw new InvalidArgumentException(sprintf(
						'Attribute "flows" is missing required key "%s".',
						$flow
					));
				}
			}
		}

		$this->flows = $flows;
	}

	public function getOpenIdConnectUrl(): ?string
	{
		return $this->openIdConnectUrl;
	}

	public function setOpenIdConnectUrl(?string $openIdConnectUrl): void
	{
		if ($this->type === self::TYPE_OPEN_ID_CONNECT && $openIdConnectUrl === null) {
			throw new InvalidArgumentException('Attribute "openIdConnectUrl" is required for type "openIdConnect".');
		}

		$this->openIdConnectUrl = $openIdConnectUrl;
	}

}
