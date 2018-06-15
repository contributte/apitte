<?php declare(strict_types = 1);

namespace Apitte\Core\Http;

interface RequestAttributes
{

	public const
		ATTR_SCHEMA = 'apitte.core.schema',
		ATTR_ENDPOINT = 'apitte.core.endpoint',
		ATTR_ROUTER = 'apitte.core.router',
		ATTR_PARAMETERS = 'apitte.core.parameters',
		ATTR_REQUEST_ENTITY = 'apitte.core.request.entity',
		ATTR_RESPONSE_ENTITY = 'apitte.core.response.entity';

}
