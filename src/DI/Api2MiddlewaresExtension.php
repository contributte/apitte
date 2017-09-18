<?php

namespace Apitte\Core\DI;

use Apitte\Core\Middlewares\ApiEmitter;
use Apitte\Core\Middlewares\ApiMiddleware;
use Apitte\Core\Middlewares\ContentNegotiation;
use Apitte\Core\Middlewares\Negotiation\SuffixNegotiator;
use Apitte\Core\Middlewares\Transformer\DebugTransformer;
use Apitte\Core\Middlewares\Transformer\JsonTransformer;
use Contributte\Middlewares\AutoBasePathMiddleware;
use Contributte\Middlewares\DI\MiddlewaresExtension;
use Contributte\Middlewares\TracyMiddleware;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\Statement;
use RuntimeException;

class Api2MiddlewaresExtension extends CompilerExtension
{

	/** @var array */
	protected $defaults = [
		'tracy' => '%debugMode%',
		'negotiation' => FALSE,
	];

	/**
	 * Register services (middlewares wrapper)
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		// Is MiddlewaresExtension (contributte/middlewares) registered?
		if (!$this->getMiddlewaresExtension()) {
			throw new RuntimeException(sprintf('Extension %s is not registered', MiddlewaresExtension::class));
		}

		$config = $this->loadConfig();

		if ($config['negotiation'] === TRUE) {
			$this->loadContentNegotiation();
		} else {
			$this->loadMiddlewares();
		}
	}

	/**
	 * Setup middlewares extension from this extension
	 * - with content negotiation
	 *
	 * @return void
	 */
	protected function loadContentNegotiation()
	{
		// HACK! Update middlewares extension
		$extension = $this->getMiddlewaresExtension();
		$config = $this->getConfig();

		$transformers = [
			'*' => new Statement(JsonTransformer::class),
			'json' => new Statement(JsonTransformer::class),
			'debug' => new Statement(DebugTransformer::class),
		];

		// No .debug transformer in production mode
		if ($config['tracy'] !== TRUE) unset($transformers['debug']);

		$extension->setConfig([
			'middlewares' => [
				new Statement(TracyMiddleware::class . '::factory', [$config['tracy']]),
				new Statement(AutoBasePathMiddleware::class),
				new Statement(ApiMiddleware::class, [[
					new Statement(ContentNegotiation::class, [[
						new Statement(SuffixNegotiator::class, [$transformers]),
					]]),
					new Statement(ApiEmitter::class),
				]]),
			],
		]);
	}

	/**
	 * Setup middlewares extension from this extension
	 *
	 * @return void
	 */
	protected function loadMiddlewares()
	{
		// HACK! Update middlewares extension
		$extension = $this->getMiddlewaresExtension();
		$config = $this->getConfig();

		$extension->setConfig([
			'middlewares' => [
				new Statement(TracyMiddleware::class . '::factory', [$config['tracy']]),
				new Statement(AutoBasePathMiddleware::class),
				new Statement(ApiMiddleware::class, [
					[new Statement(ApiEmitter::class)],
				]),
			],
		]);
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return MiddlewaresExtension
	 */
	protected function getMiddlewaresExtension()
	{
		$ext = $this->compiler->getExtensions(MiddlewaresExtension::class);

		return $ext ? reset($ext) : NULL;
	}

	/**
	 * @return array
	 */
	protected function loadConfig()
	{
		$config = $this->validateConfig($this->defaults);
		$this->config = Helpers::expand($config, $this->getContainerBuilder()->parameters);

		return $this->config;
	}

}
