<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema\Extended;

use Apitte\OpenApi\Schema\Reference;

class ComponentReference extends Reference
{

	public const
		TYPE_SCHEMA = 'schemas',
		TYPE_RESPONSE = 'responses',
		TYPE_PARAMETER = 'parameters',
		TYPE_EXAMPLE = 'examples',
		TYPE_REQUEST_BODY = 'requestBodies',
		TYPE_HEADER = 'headers',
		TYPE_SECURITY_SCHEMA = 'securitySchemes',
		TYPE_LINK = 'links',
		TYPE_CALLBACK = 'callbacks';

	public function __construct(string $type, string $name)
	{
		parent::__construct('#/components/' . $type . '/' . $name);
	}

}
