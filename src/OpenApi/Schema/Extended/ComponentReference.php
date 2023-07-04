<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema\Extended;

use Apitte\OpenApi\Schema\Reference;

class ComponentReference extends Reference
{

	public const TYPE_SCHEMA = 'schemas';
	public const TYPE_RESPONSE = 'responses';
	public const TYPE_PARAMETER = 'parameters';
	public const TYPE_EXAMPLE = 'examples';
	public const TYPE_REQUEST_BODY = 'requestBodies';
	public const TYPE_HEADER = 'headers';
	public const TYPE_SECURITY_SCHEMA = 'securitySchemes';
	public const TYPE_LINK = 'links';
	public const TYPE_CALLBACK = 'callbacks';

	public function __construct(string $type, string $name)
	{
		parent::__construct('#/components/' . $type . '/' . $name);
	}

}
