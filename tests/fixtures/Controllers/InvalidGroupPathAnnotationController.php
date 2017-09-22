<?php

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @RootPath("/foobar")
 * @GroupPath("foobar")
 */
final class InvalidGroupPathAnnotationController implements IController
{

}
