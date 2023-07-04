<?php declare(strict_types = 1);

namespace Apitte\Middlewares\DI;

use Apitte\Core\DI\Plugin\Plugin;
use Apitte\Middlewares\ApiMiddleware;
use Contributte\Middlewares\AutoBasePathMiddleware;
use Contributte\Middlewares\DI\MiddlewaresExtension;
use Contributte\Middlewares\TracyMiddleware;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class MiddlewaresPlugin extends Plugin
{

	public static function getName(): string
	{
		return 'middlewares';
	}

	/**
	 * Register services (middlewares wrappers)
	 */
	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$globalConfig = $this->compiler->getExtension()->getConfig();
		$config = $this->config;

		if ($config->tracy) {
			$builder->addDefinition($this->prefix('tracy'))
				->setFactory(TracyMiddleware::class . '::factory', [$globalConfig->debug])
				->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 100]);
		}

		if ($config->autobasepath) {
			$builder->addDefinition($this->prefix('autobasepath'))
				->setFactory(AutoBasePathMiddleware::class)
				->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 200]);
		}

		$builder->addDefinition($this->prefix('api'))
			->setFactory(ApiMiddleware::class)
			->addTag(MiddlewaresExtension::MIDDLEWARE_TAG, ['priority' => 500]);
	}

	protected function getConfigSchema(): Schema
	{
		return Expect::structure([
			'tracy' => Expect::bool(true),
			'autobasepath' => Expect::bool(true),
		]);
	}

}
