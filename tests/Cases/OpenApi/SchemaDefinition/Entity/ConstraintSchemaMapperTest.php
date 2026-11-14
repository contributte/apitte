<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\SchemaDefinition\Entity;

use Apitte\OpenApi\SchemaDefinition\Entity\ConstraintSchemaMapper;
use ReflectionClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Tester\Assert as TesterAssert;
use Tester\TestCase;

require_once __DIR__ . '/../../../../bootstrap.php';

final class ConstraintSchemaMapperTest extends TestCase
{

	public function testIntlConstraints(): void
	{
		$mapper = new ConstraintSchemaMapper();

		$country = $this->createWithoutIntl(Assert\Country::class);
		TesterAssert::same(['pattern' => '^[A-Z]{2}$'], $mapper->applyConstraint([], $country));

		$country->alpha3 = true;
		TesterAssert::same(['pattern' => '^[A-Z]{3}$'], $mapper->applyConstraint([], $country));

		TesterAssert::same(['pattern' => '^[A-Z]{3}$'], $mapper->applyConstraint([], $this->createWithoutIntl(Assert\Currency::class)));
	}

	public function testConvertPattern(): void
	{
		$mapper = new ConstraintSchemaMapper();

		TesterAssert::same('^[a-z]+$', $mapper->convertPattern('/^[a-z]+$/i'));
		TesterAssert::same('^a/b$', $mapper->convertPattern('#^a/b$#'));
		TesterAssert::same('', $mapper->convertPattern(''));
	}

	/**
	 * Country and Currency constraints require symfony/intl in their constructors
	 *
	 * @template T of Constraint
	 * @param class-string<T> $class
	 * @return T
	 */
	private function createWithoutIntl(string $class): Constraint
	{
		$constraint = (new ReflectionClass($class))->newInstanceWithoutConstructor();
		$constraint->groups = [Constraint::DEFAULT_GROUP];

		return $constraint;
	}

}

(new ConstraintSchemaMapperTest())->run();
