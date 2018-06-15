<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller as API;
use Apitte\Core\UI\Controller\IController;

/**
 * @API\Controller()
 */
class PrefixedAnnotationController implements IController
{

}
