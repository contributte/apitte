<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\SchemaDefinition\Entity;

use Apitte\OpenApi\SchemaDefinition\Entity\EntityAdapter;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Validator\Constraint;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\ResponseEntity\ConstrainedEntity;
use Tests\Fixtures\ResponseEntity\EnumEntity;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * symfony/validator is an optional dependency, the entity adapter has to work without it
 */
final class EntityAdapterWithoutValidatorTest extends TestCase
{

	public function testConstraintsAreIgnored(): void
	{
		Assert::false(class_exists(Constraint::class));

		$adapter = new EntityAdapter();
		$properties = $adapter->getMetadata(ConstrainedEntity::class)['properties'];

		Assert::same(['type' => 'string'], $properties['name']);
		Assert::same(['nullable' => true, 'type' => 'string'], $properties['email']);
		Assert::same(['type' => 'array', 'items' => ['type' => 'string']], $properties['names']);
		Assert::same(['type' => 'string', 'enum' => ['foo', 'bar']], $adapter->getMetadata(EnumEntity::class)['properties']['string']);
	}

	protected function setUp(): void
	{
		// Simulate a project without symfony/validator (each test file runs in its own process)
		foreach (ClassLoader::getRegisteredLoaders() as $loader) {
			$loader->setPsr4('Symfony\\Component\\Validator\\', []);
		}
	}

}

(new EntityAdapterWithoutValidatorTest())->run();
