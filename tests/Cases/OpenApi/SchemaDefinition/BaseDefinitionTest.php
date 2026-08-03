<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\SchemaDefinition;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\SchemaDefinition\BaseDefinition;
use Tester\Assert;
use Tester\TestCase;

final class BaseDefinitionTest extends TestCase
{

	public function testDefaultVersion(): void
	{
		$definition = new BaseDefinition();

		Assert::same('3.0.2', $definition->load()['openapi']);
		Assert::same('3.0.2', BaseDefinition::DEFAULT_VERSION);
	}

	public function testExplicitVersion(): void
	{
		$definition = new BaseDefinition('3.1.1');

		Assert::same(
			[
				'openapi' => '3.1.1',
				'info' => [
					'title' => 'OpenAPI',
					'version' => '1.0.0',
				],
				'paths' => [],
			],
			$definition->load()
		);
	}

}

(new BaseDefinitionTest())->run();
