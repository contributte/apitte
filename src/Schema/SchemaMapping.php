<?php

namespace Apitte\Core\Schema;

interface SchemaMapping
{

	const HANDLER = 'handler';
	const HANDLER_CLASS = 'class';
	const HANDLER_METHOD = 'method';
	const HANDLER_ARGUMENTS = 'arguments';

	const GROUP = 'group';
	const GROUP_IDS = 'ids';
	const GROUP_PATHS = 'paths';

	const ID = 'id';
	const METHODS = 'methods';
	const TAGS = 'tags';
	const MASK = 'mask';
	const RAW_PATTERN = 'raw_pattern';

	const PARAMETERS = 'parameters';
	const PARAMETERS_NAME = 'name';
	const PARAMETERS_TYPE = 'type';
	const PARAMETERS_PATTERN = 'pattern';
	const PARAMETERS_DESCRIPTION = 'description';

	const NEGOTIATIONS = 'negotiations';
	const NEGOTIATIONS_TYPE = 'type';
	const NEGOTIATIONS_METADATA = 'metadata';

}
