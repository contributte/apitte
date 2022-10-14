<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

use Apitte\OpenApi\Schema\Example;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class ExampleTest extends TestCase
{

	public function testAll(): void
	{
		$example = new Example();
		$summary = 'Summary';
		$description = 'Description';
		$value = 'Value';
		$externalValue = 'ExternalValue';
		$example->setSummary($summary);
		$example->setDescription($description);
		$example->setValue($value);
		$example->setExternalValue($externalValue);

		Assert::same($summary, $example->getSummary());
		Assert::same($description, $example->getDescription());
		Assert::same($value, $example->getValue());
		Assert::same($externalValue, $example->getExternalValue());

		$realData = $example->toArray();
		$expectedData = [
			'summary' => $summary,
			'description' => $description,
			'value' => $value,
			'externalValue' => $externalValue,
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Example::fromArray($realData)->toArray());
	}

}

(new ExampleTest())->run();
