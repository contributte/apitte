<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers;

use Apitte\Core\Annotation\Controller\Negotiation;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Tag;

#[Tag('nice', value: 'yes')]
#[Tag('one', value: 'no')]
final class AttributeMultiController extends ApiV1Controller
{

	#[Response('some_description', code: 'cz')]
	#[Response(description: 'some_description_2', code: 'com')]
	public function responses(): void
	{
		// Tests
	}

	#[RequestParameter(name: 'name_value', type: 'type_value', in: 'path')]
	#[RequestParameter(name: 'name_value_2', type: 'type_value_2', in: 'query')]
	public function requestParameters(): void
	{
		// Tests
	}

	#[Negotiation('some_suffix')]
	#[Negotiation(suffix: 'some_suffix_2')]
	public function negotiations(): void
	{
		// Tests
	}

}
