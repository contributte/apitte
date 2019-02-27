<?php declare(strict_types = 1);

/**
 * Test: Http\RequestScopeStorage
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Http\RequestScopeStorage;
use Tester\Assert;

test(function (): void {
	$storage = new RequestScopeStorage();

	Assert::false($storage->has('missing'));
	Assert::null($storage->load('missing'));
	Assert::same('', $storage->load('missing', ''));

	$storage->save('exists', 'foobar');
	Assert::true($storage->has('exists'));
	Assert::same('foobar', $storage->load('exists'));

	$storage->clear();

	Assert::false($storage->has('exists'));
	Assert::null($storage->load('exists'));
});
