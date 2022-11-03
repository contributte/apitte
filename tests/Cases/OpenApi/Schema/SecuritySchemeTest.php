<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\OpenApi\Schema\OAuthFlow;
use Apitte\OpenApi\Schema\SecurityScheme;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class SecuritySchemeTest extends TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function getRequiredData(): array
	{
		return [
			[
				[
					'type' => SecurityScheme::TYPE_API_KEY,
					'name' => 'api_key',
					'in' => SecurityScheme::IN_HEADER,
				],
			],
			[
				[
					'type' => SecurityScheme::TYPE_HTTP,
					'scheme' => 'basic',
				],
			],
			[
				[
					'type' => SecurityScheme::TYPE_HTTP,
					'scheme' => 'bearer',
					'bearerFormat' => 'JWT',
				],
			],
			[
				[
					'type' => SecurityScheme::TYPE_OAUTH2,
					'flows' => [
						'implicit' => [
							'authorizationUrl' => 'https://example.com/authorization',
							'tokenUrl' => 'https://example.com/token',
							'refreshUrl' => 'https://example.com/refresh',
							'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
						],
						'password' => [
							'authorizationUrl' => 'https://example.com/authorization',
							'tokenUrl' => 'https://example.com/token',
							'refreshUrl' => 'https://example.com/refresh',
							'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
						],
						'clientCredentials' => [
							'authorizationUrl' => 'https://example.com/authorization',
							'tokenUrl' => 'https://example.com/token',
							'refreshUrl' => 'https://example.com/refresh',
							'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
						],
						'authorizationCode' => [
							'authorizationUrl' => 'https://example.com/authorization',
							'tokenUrl' => 'https://example.com/token',
							'refreshUrl' => 'https://example.com/refresh',
							'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
						],
					],
				],
			],
			[
				[
					'type' => SecurityScheme::TYPE_OPEN_ID_CONNECT,
					'openIdConnectUrl' => 'https://example.com/.well-known/openid-configuration',
				],
			],
		];
	}

	/**
	 * @dataProvider getRequiredData
	 * @param mixed[] $array
	 */
	public function testRequired(array $array): void
	{
		$securityScheme = SecurityScheme::fromArray($array);
		Assert::same($array, $securityScheme->toArray());
	}

	public function testOptional(): void
	{
		$type = SecurityScheme::TYPE_API_KEY;
		$name = 'api_key';
		$in = SecurityScheme::IN_HEADER;
		$description = 'API key';
		$securityScheme = new SecurityScheme($type);
		$securityScheme->setName($name);
		$securityScheme->setIn($in);
		$securityScheme->setDescription($description);

		Assert::same($type, $securityScheme->getType());
		Assert::same($name, $securityScheme->getName());
		Assert::same($in, $securityScheme->getIn());
		Assert::same($description, $securityScheme->getDescription());

		$array = $securityScheme->toArray();
		$expected = [
			'type' => $type,
			'name' => $name,
			'description' => $description,
			'in' => $in,
		];
		Assert::same($expected, $array);
		Assert::same($expected, SecurityScheme::fromArray($array)->toArray());
	}

	public function testInvalidType(): void
	{
		Assert::exception(static function (): void {
			new SecurityScheme('invalid');
		}, InvalidArgumentException::class, 'Invalid value "invalid" for attribute "type" given. It must be one of "apiKey, http, oauth2, openIdConnect".');
	}


	public function testMissingName(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_API_KEY);
			$securityScheme->setIn(SecurityScheme::IN_HEADER);
			$securityScheme->setName(null);
		}, InvalidArgumentException::class, 'Attribute "name" is required for type "apiKey".');
	}

	public function testMissingIn(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_API_KEY);
			$securityScheme->setName('api_key');
			$securityScheme->setIn(null);
		}, InvalidArgumentException::class, 'Attribute "in" is required for type "apiKey".');
	}

	public function testInvalidIn(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_API_KEY);
			$securityScheme->setName('api_key');
			$securityScheme->setIn('invalid');
		}, InvalidArgumentException::class, 'Invalid value "invalid" for attribute "in" given. It must be one of "cookie, header, query".');
	}

	public function testMissingScheme(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_HTTP);
			$securityScheme->setScheme(null);
		}, InvalidArgumentException::class, 'Attribute "scheme" is required for type "http".');
	}

	public function testMissingBearerFormat(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_HTTP);
			$securityScheme->setScheme('bearer');
			$securityScheme->setBearerFormat(null);
		}, InvalidArgumentException::class, 'Attribute "bearerFormat" is required for type "http" and scheme "bearer".');
	}

	public function testMissingFlows(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_OAUTH2);
			$securityScheme->setFlows([]);
		}, InvalidArgumentException::class, 'Attribute "flows" is required for type "oauth2".');
	}

	public function testMissingFlow(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_OAUTH2);
			$securityScheme->setFlows([
				'implicit' => OAuthFlow::fromArray([
					'authorizationUrl' => 'https://example.com/authorization',
					'tokenUrl' => 'https://example.com/token',
					'refreshUrl' => 'https://example.com/refresh',
					'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
				]),
			]);
		}, InvalidArgumentException::class, 'Attribute "flows" is missing required key "password".');
	}

	public function testMissingOpenIdConnectUrl(): void
	{
		Assert::exception(static function (): void {
			$securityScheme = new SecurityScheme(SecurityScheme::TYPE_OPEN_ID_CONNECT);
			$securityScheme->setOpenIdConnectUrl(null);
		}, InvalidArgumentException::class, 'Attribute "openIdConnectUrl" is required for type "openIdConnect".');
	}

}

(new SecuritySchemeTest())->run();
