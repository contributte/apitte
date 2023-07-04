<?php declare(strict_types = 1);

namespace Apitte\Debug\Tracy\Panel;

use Apitte\Core\Schema\Schema;
use Tracy\IBarPanel;

class ApiPanel implements IBarPanel
{

	private Schema $schema;

	public function __construct(Schema $schema)
	{
		$this->schema = $schema;
	}

	/**
	 * Renders HTML code for custom tab.
	 */
	public function getTab(): string
	{
		// phpcs:disable
		ob_start();
		$schema = $this->schema;
		require __DIR__ . '/templates/tab.phtml';

		return (string) ob_get_clean();
		// phpcs:enable
	}

	/**
	 * Renders HTML code for custom panel.
	 */
	public function getPanel(): string
	{
		// phpcs:disable
		ob_start();
		$schema = $this->schema;
		require __DIR__ . '/templates/panel.phtml';

		return (string) ob_get_clean();
		// phpcs:enable
	}

}
