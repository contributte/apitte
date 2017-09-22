<?php

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\Group;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @RootPath("/foobar")
 * @Group("/foobar")
 */
final class InvalidGroupAnnotationController implements IController
{

}
