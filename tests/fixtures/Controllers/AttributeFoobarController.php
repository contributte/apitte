<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Id;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\OpenApi;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

#[Path('/foobar')]
#[Id('foobar')]
final class AttributeFoobarController extends ApiV1Controller
{

	#[Path('/baz1')]
	#[Method(['GET'])]
	#[Id('baz1')]
	public function baz1(ApiRequest $request, ApiResponse $response): void
	{
	}

	#[Path('/baz2')]
	#[Method(['GET', 'POST'])]
	public function baz2(ApiRequest $request, ApiResponse $response): void
	{
	}

	#[Path('/baz2')]
	#[Method(['PUT'])]
	public function baz3(ApiRequest $request, ApiResponse $response): void
	{
	}

	#[Path('/openapi')]
	#[Method(['PUT'])]
	#[OpenApi(<<<'DOC'
		foo:
			bar: baz
		lorem:
			- ipsum
			- dolor
			- sit
			- amet
	DOC
	)]
	public function openapi(ApiRequest $request, ApiResponse $response): void
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
