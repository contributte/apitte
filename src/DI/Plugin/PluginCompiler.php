<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\ApiExtension;

class PluginCompiler
{

	/** @var ApiExtension */
	protected $extension;

	/**
	 * @param ApiExtension $extension
	 */
	public function __construct(ApiExtension $extension)
	{
		$this->extension = $extension;
	}

	/**
	 * @return ApiExtension
	 */
	public function getExtension()
	{
		return $this->extension;
	}

}
