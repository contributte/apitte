<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

/**
 * A definition whose output depends on the OpenAPI version of the document.
 * SchemaBuilder resolves the version first and hands it over before loading.
 */
interface IVersionAwareDefinition extends IDefinition
{

	public function setVersion(string $version): void;

}
