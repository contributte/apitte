<?php declare(strict_types = 1);

namespace Tests\Cases\OpenApi\Cases\Schema;

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\OpenApi\Schema\Contact;
use Apitte\OpenApi\Schema\Info;
use Apitte\OpenApi\Schema\License;
use Tester\Assert;
use Tester\TestCase;

class InfoTest extends TestCase
{

	public function testOptional(): void
	{
		$info = new Info('Sample Pet Store App', '1.0.1');
		$info->setDescription('This is a sample server for a pet store.');
		$info->setTermsOfService('http://example.com/terms/');

		$contact = new Contact();
		$info->setContact($contact);

		$license = new License('Apache 2.0');
		$info->setLicense($license);

		Assert::same('Sample Pet Store App', $info->getTitle());
		Assert::same('This is a sample server for a pet store.', $info->getDescription());
		Assert::same('http://example.com/terms/', $info->getTermsOfService());
		Assert::same($contact, $info->getContact());
		Assert::same($license, $info->getLicense());
		Assert::same('1.0.1', $info->getVersion());

		$realData = $info->toArray();
		$expectedData = [
			'title' => 'Sample Pet Store App',
			'description' => 'This is a sample server for a pet store.',
			'termsOfService' => 'http://example.com/terms/',
			'contact' => [],
			'license' => ['name' => 'Apache 2.0'],
			'version' => '1.0.1',
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Info::fromArray($realData)->toArray());
	}

	public function testRequired(): void
	{
		$info = new Info('Sample Pet Store App', '1.0.1');

		Assert::same('Sample Pet Store App', $info->getTitle());
		Assert::same(null, $info->getDescription());
		Assert::same(null, $info->getTermsOfService());
		Assert::same(null, $info->getContact());
		Assert::same(null, $info->getLicense());
		Assert::same('1.0.1', $info->getVersion());

		$realData = $info->toArray();
		$expectedData = [
			'title' => 'Sample Pet Store App',
			'version' => '1.0.1',
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, Info::fromArray($realData)->toArray());
	}

}

(new InfoTest())->run();
