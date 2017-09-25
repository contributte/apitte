<?php

/**
 * Test: Annotation\Controller\RequestMapper
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestMapper;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Value
test(function () {
	$requestMapper = new RequestMapper(['value' => 'RequestMapper']);
	Assert::equal('RequestMapper', $requestMapper->getEntity());
	Assert::exception(function () {
		new RequestMapper(['value' => '']);
	}, AnnotationException::class, 'Empty @RequestMapper given');
});

// Entity
test(function () {
	$RequestMapper = new RequestMapper(['entity' => 'Entity']);
	Assert::equal('Entity', $RequestMapper->getEntity());

	Assert::exception(function () {
		new RequestMapper(['entity' => '']);
	}, AnnotationException::class, 'Empty @RequestMapper given');
});

// Fails
test(function () {
	Assert::exception(function () {
		new RequestMapper(['entity']);
	}, AnnotationException::class, 'No @RequestMapper given');
	Assert::exception(function () {
		new RequestMapper(['value']);
	}, AnnotationException::class, 'No @RequestMapper given');
	Assert::exception(function () {
		new RequestMapper(['a']);
	}, AnnotationException::class, 'No @RequestMapper given');
	Assert::exception(function () {
		new RequestMapper([]);
	}, AnnotationException::class, 'No @RequestMapper given');
});
