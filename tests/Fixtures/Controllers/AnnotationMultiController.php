<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Negotiation;
use Apitte\Core\Annotation\Controller\Negotiations;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Tag;

/**
 * @Tag("nice", value="yes")
 * @Tag("one", value="no")
 */
final class AnnotationMultiController extends ApiV1Controller
{

	/**
	 * @Responses({
	 * 		@Response("some_description", code="cz"),
	 * 		@Response(description="some_description_2", code="com")
	 * })
	 */
	public function responses(): void
	{
		// Tests
	}

	/**
	 * @RequestParameters({
	 *		@RequestParameter(name="name_value", type="type_value", in="path"),
	 *		@RequestParameter(in="query", type="type_value_2", name="name_value_2")
	 * })
	 */
	public function requestParameters(): void
	{
		// Tests
	}

	/**
	 * @Negotiations({
	 * 		@Negotiation("some_suffix"),
	 * 		@Negotiation(suffix="some_suffix_2")
	 * })
	 */
	public function negotiations(): void
	{
		// Tests
	}

}
