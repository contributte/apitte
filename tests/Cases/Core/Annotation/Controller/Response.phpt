<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\Response;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// OK
Toolkit::test(function (): void {
	$response = new Response(
		description: 'name',
		code: '200',
		entity: 'EntityClass'
	);

	Assert::same('name', $response->getDescription());
	Assert::same('200', $response->getCode());
	Assert::same('EntityClass', $response->getEntity());
});

// OK - default values
Toolkit::test(function (): void {
	$response = new Response('name');

	Assert::same('name', $response->getDescription());
	Assert::same('default', $response->getCode());
	Assert::null($response->getEntity());
});

// Exception - empty description
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new Response('');
	}, InvalidArgumentException::class, 'Empty #[Response] description given');
});
