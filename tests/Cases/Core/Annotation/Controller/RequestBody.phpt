<?php declare(strict_types = 1);

/**
 * Test: Annotation\Controller\RequestBody
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestBody;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;

// OK
test(function (): void {
	$requestBody1 = new RequestBody();
	Assert::same(null, $requestBody1->getEntity());
	Assert::same(true, $requestBody1->isValidation());
	Assert::same(null, $requestBody1->getDescription());
	Assert::same(false, $requestBody1->isRequired());

	$requestBody2 = new RequestBody(
		'description',
		FooEntity::class,
		true,
		false
	);
	Assert::same(FooEntity::class, $requestBody2->getEntity());
	Assert::same(false, $requestBody2->isValidation());
	Assert::same('description', $requestBody2->getDescription());
	Assert::same(true, $requestBody2->isRequired());
});
