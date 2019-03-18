<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @ControllerPath("/foobar")
 * @GroupPath("foobar")
 */
final class InvalidGroupPathAnnotationController implements IController
{

}
