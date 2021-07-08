<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;

final class AnnotationAttributeController extends ApiV1Controller
{

	/**
	 * @RequestParameters({
	 *     @RequestParameter(in="path", type="int", name="userId", description="User ID"),
	 * })
	 */
	#[Path(path: '/user/{userId}/collections')]
	public function run()
	{
	}

}
