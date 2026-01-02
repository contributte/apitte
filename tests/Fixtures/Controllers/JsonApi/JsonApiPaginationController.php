<?php declare(strict_types = 1);

namespace Tests\Fixtures\Controllers\JsonApi;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Tests\Fixtures\Controllers\ApiV1Controller;

/**
 * Example controller demonstrating JSON:API style request parameters.
 *
 * @see https://jsonapi.org/format/#fetching-pagination
 * @see https://jsonapi.org/format/#fetching-filtering
 */
#[Path('/articles')]
final class JsonApiPaginationController extends ApiV1Controller
{

	/**
	 * List articles with JSON:API pagination.
	 *
	 * Example request: GET /api/v1/articles?page[number]=2&page[size]=25
	 */
	#[Path('/')]
	#[Method('GET')]
	#[RequestParameter(name: 'page[number]', type: 'int', in: 'query', required: false, description: 'Page number (JSON:API style)')]
	#[RequestParameter(name: 'page[size]', type: 'int', in: 'query', required: false, description: 'Page size (JSON:API style)')]
	public function list(): void
	{
		// Controller implementation
		// Access via: $request->getParameter('page[number]')
	}

	/**
	 * List articles with JSON:API filtering.
	 *
	 * Example request: GET /api/v1/articles/filtered?filter[status]=published&filter[author][id]=123
	 */
	#[Path('/filtered')]
	#[Method('GET')]
	#[RequestParameter(name: 'filter[status]', type: 'string', in: 'query', required: false, description: 'Filter by status')]
	#[RequestParameter(name: 'filter[author][id]', type: 'int', in: 'query', required: false, description: 'Filter by author ID')]
	public function filtered(): void
	{
		// Controller implementation
		// Access via: $request->getParameter('filter[status]')
		// Access via: $request->getParameter('filter[author][id]')
	}

	/**
	 * Alternative colon notation for pagination.
	 *
	 * Example request: GET /api/v1/articles/alt?page:number=2&page:size=25
	 * Note: Query params must be nested manually or via middleware.
	 */
	#[Path('/alt')]
	#[Method('GET')]
	#[RequestParameter(name: 'page:number', type: 'int', in: 'query', required: false, description: 'Page number (colon notation)')]
	#[RequestParameter(name: 'page:size', type: 'int', in: 'query', required: false, description: 'Page size (colon notation)')]
	public function altPagination(): void
	{
		// Controller implementation
	}

}
