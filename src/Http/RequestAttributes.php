<?php

namespace Apitte\Core\Http;

interface RequestAttributes
{

	const ATTR_SCHEMA = 'apitte.core.schema';
	const ATTR_ENDPOINT = 'apitte.core.endpoint';
	const ATTR_ROUTER = 'apitte.core.router';
	const ATTR_PARAMETERS = 'apitte.core.parameters';
	const ATTR_REQUEST_ENTITY = 'apitte.core.request.entity';
	const ATTR_RESPONSE_ENTITY = 'apitte.core.response.entity';

}
