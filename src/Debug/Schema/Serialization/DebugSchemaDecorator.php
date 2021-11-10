<?php declare(strict_types = 1);

namespace Apitte\Debug\Schema\Serialization;

use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Serialization\IDecorator;

final class DebugSchemaDecorator implements IDecorator
{

	public function decorate(SchemaBuilder $builder): SchemaBuilder
	{
		foreach ($builder->getControllers() as $controller) {
			foreach ($controller->getMethods() as $method) {
				$method->addNegotiation('.debugdata');
				$method->addNegotiation('.debug');
			}
		}

		return $builder;
	}

}
