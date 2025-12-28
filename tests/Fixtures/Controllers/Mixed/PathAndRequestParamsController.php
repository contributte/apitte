<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers\Mixed;

use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Tests\Fixtures\Controllers\ApiV1Controller;

final class PathAndRequestParamsController extends ApiV1Controller
{

	#[RequestParameter(name: 'userId', type: 'int', description: 'User ID', in: 'path')]
	#[RequestParameter(name: 'photoId', type: 'int', description: 'Verification photo ID', in: 'path')]
	#[Path(path: '/user/{userId}/verification-photo/{photoId}')]
	public function run(): void
	{
		// Tests
	}

}
