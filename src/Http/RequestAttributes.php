<?php declare(strict_types = 1);

namespace Apitte\Core\Http;

interface RequestAttributes
{

	public const ATTR_SCHEMA = 'apitte.core.schema';
	public const ATTR_ENDPOINT = 'apitte.core.endpoint';
	public const ATTR_ROUTER = 'apitte.core.router';
	public const ATTR_PARAMETERS = 'apitte.core.parameters';
	public const ATTR_REQUEST_ENTITY = 'apitte.core.request.entity';
	public const ATTR_RESPONSE_ENTITY = 'apitte.core.response.entity';

}
