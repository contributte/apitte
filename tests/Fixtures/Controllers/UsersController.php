<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

#[Apitte\Path('/users')]
#[Apitte\Id('users')]
final class UsersController extends ApiV1Controller
{

	#[Apitte\Path('/')]
	#[Apitte\Method(['GET'])]
	#[Apitte\Id('list')]
	public function list(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

	#[Apitte\Path('/{id}')]
	#[Apitte\Method(['GET'])]
	#[Apitte\Id('detail')]
	#[Apitte\RequestParameter(name: 'id', type: 'int', in: 'path')]
	public function detail(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

}
