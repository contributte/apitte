<?php declare(strict_types = 1);

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiResponse;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(static function (): void {
	$previous = new Exception('previous');

	$exception = ClientErrorException::create()
		->withCode(ApiResponse::S406_NOT_ACCEPTABLE)
		->withMessage('test')
		->withPrevious($previous)
		->withTypedContext('foo', 'bar');

	Assert::same(ApiResponse::S406_NOT_ACCEPTABLE, $exception->getCode());
	Assert::same('test', $exception->getMessage());
	Assert::same($previous, $exception->getPrevious());
	Assert::same(['foo' => 'bar'], $exception->getContext());
});
