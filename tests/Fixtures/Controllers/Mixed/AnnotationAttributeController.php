<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers\Mixed;

use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Tests\Fixtures\Controllers\ApiV1Controller;

final class AnnotationAttributeController extends ApiV1Controller
{

	#[RequestParameter(name: 'userId', type: 'int', in: 'path', description: 'User ID')]
	#[Path(path: '/user/{userId}/collections')]
	public function run(): void
	{
		// Tests
	}

}
