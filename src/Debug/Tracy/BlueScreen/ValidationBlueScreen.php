<?php declare(strict_types = 1);

namespace Apitte\Debug\Tracy\BlueScreen;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use ReflectionClass;
use Tracy\BlueScreen;
use Tracy\Helpers;

class ValidationBlueScreen
{

	public static function register(BlueScreen $blueScreen): void
	{
		$blueScreen->addPanel(static function (?\Throwable $e): ?array {
			if (!($e instanceof InvalidSchemaException)) {
				return null;
			}

			$panel = self::renderPanel($e);
			if ($panel === null) {
				return null;
			}

			return [
				'tab' => self::renderTab($e),
				'panel' => $panel,
			];
		});
	}

	private static function renderTab(InvalidSchemaException $e): string
	{
		return 'Apitte - Validation';
	}

	private static function renderPanel(InvalidSchemaException $e): ?string
	{
		if ($e->controller === null || $e->method === null || !class_exists($e->controller->getClass())) {
			return null;
		}

		$rf = new ReflectionClass($e->controller->getClass());
		$rm = $rf->getMethod($e->method->getName());

		return '<p><b>File:</b>' . Helpers::editorLink($rf->getFileName(), $rm->getStartLine()) . '</p>'
			. BlueScreen::highlightFile((string) $rf->getFileName(), (int) $rm->getStartLine(), 20);
	}

}
