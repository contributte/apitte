<?php

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @ControllerPath("/foobar")
 * @GroupPath("foobar")
 */
final class InvalidGroupPathAnnotationController implements IController
{

}
