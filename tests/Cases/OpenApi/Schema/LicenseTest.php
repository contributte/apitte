<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\License;
use Tester\Assert;
use Tester\TestCase;

class LicenseTest extends TestCase
{

	public function testOptional(): void
	{
		$license = new License('Apache 2.0');
		$license->setUrl('https://www.apache.org/licenses/LICENSE-2.0.html');

		Assert::same('Apache 2.0', $license->getName());
		Assert::same('https://www.apache.org/licenses/LICENSE-2.0.html', $license->getUrl());

		$realData = $license->toArray();
		$expectedData = [
			'name' => 'Apache 2.0',
			'url' => 'https://www.apache.org/licenses/LICENSE-2.0.html',
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, License::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$license = new License('Apache 2.0');

		Assert::same('Apache 2.0', $license->getName());
		Assert::null($license->getUrl());

		$realData = $license->toArray();
		$expectedData = ['name' => 'Apache 2.0'];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, License::fromArray($realData)->toArray());
	}

}

(new LicenseTest())->run();
