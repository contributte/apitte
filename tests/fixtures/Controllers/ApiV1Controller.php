<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\GroupPath;

/**
 * @GroupPath("/v1")
 */
abstract class ApiV1Controller extends AbstractController
{

}
