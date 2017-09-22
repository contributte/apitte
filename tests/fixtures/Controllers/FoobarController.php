<?php

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

/**
 * @RootPath("/foobar")
 */
final class FoobarController extends ApiV1Controller
{

	/**
	 * @Path("/baz1")
	 * @Method("GET")
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return void
	 */
	public function baz1(ApiRequest $request, ApiResponse $response)
	{
	}

	/**
	 * @Path("/baz2")
	 * @Method({"GET", "POST"})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return void
	 */
	public function baz2(ApiRequest $request, ApiResponse $response)
	{
	}

	/**
	 * @Path("/baz2")
	 * @Method(methods={"PUT"})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return void
	 */
	public function baz3(ApiRequest $request, ApiResponse $response)
	{
	}

	/**
	 * @return void
	 */
	public function getData1()
	{
		// Skip this method
	}

	/**
	 * @return void
	 */
	protected function getData2()
	{
		// Skip this method
	}

	/**
	 * @return void
	 */
	protected function getData3()
	{
		// Skip this method
	}

}
