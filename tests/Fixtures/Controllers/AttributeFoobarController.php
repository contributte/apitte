<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

#[Apitte\Path('/foobar')]
#[Apitte\Id('foobar')]
final class AttributeFoobarController extends ApiV1Controller
{

	#[Apitte\Path('/baz1')]
	#[Apitte\Method(['GET'])]
	#[Apitte\Id('baz1')]
	public function baz1(ApiRequest $request, ApiResponse $response): void
	{
		// Tests
	}

	#[Apitte\Path('/baz2')]
	#[Apitte\Method(['GET', 'POST'])]
	public function baz2(ApiRequest $request, ApiResponse $response): void
	{
		// Tests
	}

	#[Apitte\Path('/baz2')]
	#[Apitte\Method(['PUT'])]
	public function baz3(ApiRequest $request, ApiResponse $response): void
	{
		// Tests
	}

	#[Apitte\Path('/openapi')]
	#[Apitte\Method(['PUT'])]
	#[Apitte\OpenApi(<<<'NEON'
		foo:
			bar: baz
		lorem:
			- ipsum
			- dolor
			- sit
			- amet
	NEON
	)]
	public function openapi(ApiRequest $request, ApiResponse $response): void
	{
		// Tests
	}

	public function getData1(): void
	{
		// Tests
	}

	protected function getData2(): void
	{
		// Tests
	}

	protected function getData3(): void
	{
		// Tests
	}

}
