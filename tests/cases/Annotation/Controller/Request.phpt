<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\RequestMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Request;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;

// OK
test(function (): void {
	$requestMapper1 = new Request([]);
	Assert::same(null, $requestMapper1->getEntity());
	Assert::same(true, $requestMapper1->isValidation());
	Assert::same(null, $requestMapper1->getDescription());
	Assert::same(false, $requestMapper1->isRequired());

	$requestMapper2 = new Request([
		'entity' => FooEntity::class,
		'validation' => false,
		'required' => true,
		'description' => 'description',
	]);
	Assert::same(FooEntity::class, $requestMapper2->getEntity());
	Assert::same(false, $requestMapper2->isValidation());
	Assert::same('description', $requestMapper2->getDescription());
	Assert::same(true, $requestMapper2->isRequired());
});
