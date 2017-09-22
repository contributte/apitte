<?php

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\Group;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @Group("testapi")
 * @GroupPath("/api")
 */
abstract class AbstractController implements IController
{

}
