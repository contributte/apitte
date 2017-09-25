<?php

/**
 * Test: Annotation\Controller\RequestParameter
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Annotation\Controller\RequestParameter;
use Doctrine\Common\Annotations\AnnotationException;
use Tester\Assert;

// Success
test(function () {
	$requestParameter = new RequestParameter(['name' => 'Parameter', 'description' => 'Desc']);
	Assert::equal('Parameter', $requestParameter->getName());
	Assert::equal('Desc', $requestParameter->getDescription());
	Assert::null($requestParameter->getType());

	$requestParameter = new RequestParameter(['name' => 'Parameter', 'type' => 'Type']);
	Assert::equal('Parameter', $requestParameter->getName());
	Assert::equal('Type', $requestParameter->getType());
	Assert::null($requestParameter->getDescription());

	$requestParameter = new RequestParameter(['name' => 'Parameter', 'description' => 'Desc', 'type' => 'Type']);
	Assert::equal('Parameter', $requestParameter->getName());
	Assert::equal('Desc', $requestParameter->getDescription());
	Assert::equal('Type', $requestParameter->getType());
});

// Fails
test(function () {
	Assert::exception(function () {
		new RequestParameter([]);
	}, AnnotationException::class, 'Name is required at @RequestParameter');
	Assert::exception(function () {
		new RequestParameter(['value' => '']);
	}, AnnotationException::class, 'Name is required at @RequestParameter');
	Assert::exception(function () {
		new RequestParameter(['name' => 'Param']);
	}, AnnotationException::class, 'Type or description is required at @RequestParameter');
});
