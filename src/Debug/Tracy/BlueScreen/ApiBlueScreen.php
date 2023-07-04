<?php declare(strict_types = 1);

namespace Apitte\Debug\Tracy\BlueScreen;

use Apitte\Core\Exception\ApiException;
use Tracy\BlueScreen;
use Tracy\Dumper;

class ApiBlueScreen
{

	public static function register(BlueScreen $blueScreen): void
	{
		$blueScreen->addPanel(static function ($e): ?array {
			if (!($e instanceof ApiException)) {
				return null;
			}

			if (!$e->getContext()) {
				return null;
			}

			return [
				'tab' => self::renderTab($e),
				'panel' => self::renderPanel($e),
			];
		});
	}

	private static function renderTab(ApiException $e): string
	{
		return 'Apitte';
	}

	private static function renderPanel(ApiException $e): string
	{
		return Dumper::toHtml($e->getContext());
	}

}
