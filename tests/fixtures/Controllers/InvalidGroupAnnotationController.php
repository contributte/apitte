<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\GroupId;
use Apitte\Core\UI\Controller\IController;

/**
 * @ControllerPath("/foobar")
 * @GroupId("/foobar")
 */
final class InvalidGroupAnnotationController implements IController
{

}
