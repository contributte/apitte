<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

/**
 * @ControllerPath("/foobar")
 */
final class FoobarController extends ApiV1Controller
{

	/**
	 * @Path("/baz1")
	 * @Method("GET")
	 */
	public function baz1(ApiRequest $request, ApiResponse $response): void
	{
	}

	/**
	 * @Path("/baz2")
	 * @Method({"GET", "POST"})
	 */
	public function baz2(ApiRequest $request, ApiResponse $response): void
	{
	}

	/**
	 * @Path("/baz2")
	 * @Method({"PUT"})
	 */
	public function baz3(ApiRequest $request, ApiResponse $response): void
	{
	}

	public function getData1(): void
	{
		// Skip this method
	}

	protected function getData2(): void
	{
		// Skip this method
	}

	protected function getData3(): void
	{
		// Skip this method
	}

}
