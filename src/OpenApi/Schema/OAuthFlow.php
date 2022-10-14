<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class OAuthFlow
{

	/** @var string */
	private string $authorizationUrl;

	/** @var string */
	private string $tokenUrl;

	/** @var string */
	private string $refreshUrl;

	/** @var array<string, string> */
	private array $scopes;

	/**
	 * @param string $authorizationUrl
	 * @param string $tokenUrl
	 * @param string $refreshUrl
	 * @param array<string, string> $scopes
	 */
	public function __construct(string $authorizationUrl, string $tokenUrl, string $refreshUrl, array $scopes)
	{
		$this->authorizationUrl = $authorizationUrl;
		$this->tokenUrl = $tokenUrl;
		$this->refreshUrl = $refreshUrl;
		$this->scopes = $scopes;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'authorizationUrl' => $this->authorizationUrl,
			'tokenUrl' => $this->tokenUrl,
			'refreshUrl' => $this->refreshUrl,
			'scopes' => $this->scopes,
		];
	}

	/**
	 * @param mixed[] $data
	 * @return self
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			$data['authorizationUrl'],
			$data['tokenUrl'],
			$data['refreshUrl'],
			$data['scopes'],
		);
	}

	public function getAuthorizationUrl(): string
	{
		return $this->authorizationUrl;
	}

	public function setAuthorizationUrl(string $authorizationUrl): void
	{
		$this->authorizationUrl = $authorizationUrl;
	}

	public function getTokenUrl(): string
	{
		return $this->tokenUrl;
	}

	public function setTokenUrl(string $tokenUrl): void
	{
		$this->tokenUrl = $tokenUrl;
	}

	public function getRefreshUrl(): string
	{
		return $this->refreshUrl;
	}

	public function setRefreshUrl(string $refreshUrl): void
	{
		$this->refreshUrl = $refreshUrl;
	}

	/**
	 * @return array<string, string>
	 */
	public function getScopes(): array
	{
		return $this->scopes;
	}

	/**
	 * @param array<string, string> $scopes
	 */
	public function setScopes(array $scopes): void
	{
		$this->scopes = $scopes;
	}

}
