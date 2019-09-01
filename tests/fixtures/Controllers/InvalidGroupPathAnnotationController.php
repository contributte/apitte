<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\UI\Controller\IController;

/**
 * @Path("/foobar")
 * @GroupPath("foobar")
 */
final class InvalidGroupPathAnnotationController implements IController
{

}
