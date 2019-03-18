<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\GroupId;
use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @GroupId("testapi")
 * @GroupPath("/api")
 */
abstract class AbstractController implements IController
{

}
