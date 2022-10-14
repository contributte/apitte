<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

use Apitte\OpenApi\Schema\OAuthFlow;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class OAuthFlowTest extends TestCase
{

	public function testRequired(): void
	{
		$authorizationUrl = 'https://example.com/authorization';
		$tokenUrl = 'https://example.com/token';
		$refreshUrl = 'https://example.com/refresh';
		$scopes = ['read' => 'Read access', 'write' => 'Write access'];
		$flow = new OAuthFlow($authorizationUrl, $tokenUrl, $refreshUrl, $scopes);

		Assert::same($authorizationUrl, $flow->getAuthorizationUrl());
		Assert::same($tokenUrl, $flow->getTokenUrl());
		Assert::same($refreshUrl, $flow->getRefreshUrl());
		Assert::same($scopes, $flow->getScopes());

		$realData = $flow->toArray();
		$expectedData = [
			'authorizationUrl' => $authorizationUrl,
			'tokenUrl' => $tokenUrl,
			'refreshUrl' => $refreshUrl,
			'scopes' => $scopes,
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, OAuthFlow::fromArray($realData)->toArray());
	}

}

(new OAuthFlowTest())->run();
