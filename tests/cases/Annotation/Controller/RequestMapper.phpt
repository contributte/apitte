<?php

/**
 * Test: Annotation\Controller\RequestMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestMapper;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Entity
test(function () {
	$requestMapper = new RequestMapper(['entity' => 'Entity']);
	Assert::equal('Entity', $requestMapper->getEntity());

	Assert::exception(function () {
		new RequestMapper(['entity' => '']);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
});

// Fails
test(function () {
	Assert::exception(function () {
		new RequestMapper(['entity']);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
	Assert::exception(function () {
		new RequestMapper(['a']);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
	Assert::exception(function () {
		new RequestMapper([]);
	}, AnnotationException::class, 'Empty @RequestMapper entity given');
});
