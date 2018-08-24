<?php declare(strict_types = 1);

/**
 * Test: Mapping\RequestParameterMapping
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Parameter\FloatTypeMapper;
use Apitte\Core\Mapping\Parameter\IntegerTypeMapper;
use Apitte\Core\Mapping\Parameter\StringTypeMapper;
use Apitte\Core\Mapping\RequestParameterMapping;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Tester\Assert;
use Tester\TestCase;

final class TestRequestParameterMapping extends TestCase
{

	/** @var RequestParameterMapping */
	private $requestParameterMapping;

	/** @var ApiRequest */
	private $request;

	/** @var ApiResponse */
	private $response;

	protected function setUp(): void
	{
		$this->requestParameterMapping = new RequestParameterMapping();

		$this->requestParameterMapping->addMapper('string', new StringTypeMapper());
		$this->requestParameterMapping->addMapper('int', new IntegerTypeMapper());
		$this->requestParameterMapping->addMapper('float', new FloatTypeMapper());

		$this->request = new ApiRequest(
			new ServerRequest(
				'GET',
				'/'
			)
		);
		$this->response = new ApiResponse(new Response());
	}

	public function testIntInPath(): void
	{
		$handler = new EndpointHandler('class', 'method');

		$endpoint = new Endpoint($handler);

		$idEndpointParameter = new EndpointParameter('id', EndpointParameter::TYPE_INTEGER);
		$idEndpointParameter->setIn($idEndpointParameter::IN_PATH);
		$idEndpointParameter->setRequired(false);
		$idEndpointParameter->setAllowEmpty(true);

		$endpoint->addParameter($idEndpointParameter);

		$request = $this->request
			->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, [
				'id' => null,
			]);

		// ---- test optional parameter

		$notRequiredIdResponse = $this->requestParameterMapping->map(
			$request,
			$this->response
		);

		Assert::null($notRequiredIdResponse->getAttribute(RequestAttributes::ATTR_PARAMETERS)['id']);

		// ---- test throw missing parameter

		$idEndpointParameter->setRequired(true);

		Assert::throws(
			function () use ($request): void {
				$this->requestParameterMapping->map($request, $this->response);
			},
			ClientErrorException::class,
			'Parameter "id" should be provided in request attributes',
			400
		);

		// ---- test correct map int parameter

		$requestWithId = $request->withAttribute(RequestAttributes::ATTR_PARAMETERS, [
			'id' => '10',
		]);

		$requiredIdResponse = $this->requestParameterMapping->map($requestWithId, $this->response);

		Assert::true(
			array_key_exists(
				'id',
				$requiredIdResponse->getAttribute(RequestAttributes::ATTR_PARAMETERS)
			)
		);
		Assert::same(10, $requiredIdResponse->getAttribute(RequestAttributes::ATTR_PARAMETERS)['id']);

		// ---- test throw empty parameter

		$requestWithEmptyId = $request->withAttribute(RequestAttributes::ATTR_PARAMETERS, [
			'id' => '',
		]);
		$idEndpointParameter->setAllowEmpty(false);

		Assert::throws(
			function () use ($requestWithEmptyId): void {
				$this->requestParameterMapping->map($requestWithEmptyId, $this->response);
			},
			ClientErrorException::class,
			'Parameter "id" should not be empty',
			400
		);
	}

	public function testFloatInQuery(): void
	{
		$handler = new EndpointHandler('class', 'method');

		$endpoint = new Endpoint($handler);

		$scoreEndpointParameter = new EndpointParameter('score', EndpointParameter::TYPE_FLOAT);
		$scoreEndpointParameter->setIn($scoreEndpointParameter::IN_QUERY);
		$scoreEndpointParameter->setRequired(false);
		$scoreEndpointParameter->setAllowEmpty(true);

		$endpoint->addParameter($scoreEndpointParameter);

		$request = $this->request
			->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, [
				'score' => null,
			]);

		$noScoreResponse = $this->requestParameterMapping->map($request, $this->response);

		Assert::equal(['score' => null], $noScoreResponse->getAttribute(RequestAttributes::ATTR_PARAMETERS));

		$requestWithIdAndScore = $request->withAttribute(
			RequestAttributes::ATTR_PARAMETERS,
			[
				'score' => '3.33',
			]
		);

		$scoreResponse = $this->requestParameterMapping->map(
			$requestWithIdAndScore,
			$this->response
		);

		Assert::equal(
			[
				'score' => 3.33,
			],
			$scoreResponse->getAttribute(RequestAttributes::ATTR_PARAMETERS)
		);

		$scoreEndpointParameter->setRequired(true);

		Assert::throws(
			function () use ($request): void {
				$this->requestParameterMapping->map($request, $this->response);
			},
			ClientErrorException::class,
			'Parameter "score" should be provided in request attributes',
			400
		);
	}

	public function testStringInCookie(): void
	{
		$handler = new EndpointHandler('class', 'method');

		$endpoint = new Endpoint($handler);

		$sessionEndpointParameter = new EndpointParameter('session', EndpointParameter::TYPE_STRING);
		$sessionEndpointParameter->setIn($sessionEndpointParameter::IN_COOKIE);
		$sessionEndpointParameter->setRequired(false);
		$sessionEndpointParameter->setAllowEmpty(false);

		$endpoint->addParameter($sessionEndpointParameter);

		$request = $this->request
			->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, [])
			->withCookieParams(
				[
					'session' => null,
				]
			);

		$responseWithoutCookie = $this->requestParameterMapping->map($request, $this->response);
		Assert::equal(['session' => null], $responseWithoutCookie->getCookieParams());

		$requestWithEmptyCookie = $request->withCookieParams(
			[
				'session' => '',
			]
		);

		Assert::throws(
			function () use ($requestWithEmptyCookie): void {
				$this->requestParameterMapping->map($requestWithEmptyCookie, $this->response);
			},
			ClientErrorException::class,
			'Parameter "session" should not be empty',
			400
		);

		$requestWithCookie = $request->withCookieParams(
			[
				'session' => 'bar-baz-key',
			]
		);

		$cookieResponse = $this->requestParameterMapping->map(
			$requestWithCookie,
			$this->response
		);

		Assert::equal(['session' => 'bar-baz-key'], $cookieResponse->getCookieParams());
	}

	public function testStringInHeader(): void
	{
		$handler = new EndpointHandler('class', 'method');

		$endpoint = new Endpoint($handler);

		$authEndpointParameter = new EndpointParameter('auth', EndpointParameter::TYPE_STRING);
		$authEndpointParameter->setIn($authEndpointParameter::IN_HEADER);
		$authEndpointParameter->setRequired(true);
		$authEndpointParameter->setAllowEmpty(false);

		$endpoint->addParameter($authEndpointParameter);

		$request = $this->request
			->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, []);

		$requestWithEmptyHeader = $request->withHeader(
			'auth',
			[
				'some',
				'',
			]
		);

		Assert::throws(
			function () use ($requestWithEmptyHeader): void {
				$this->requestParameterMapping->map($requestWithEmptyHeader, $this->response);
			},
			ClientErrorException::class,
			'Parameter "auth" should not be empty',
			400
		);

		$requestWithHeader = $request->withHeader(
			'auth',
			[
				'some',
				'other',
			]
		);

		$headerResponse = $this->requestParameterMapping->map($requestWithHeader, $this->response);

		Assert::equal(
			[
				'some',
				'other',
			],
			$headerResponse->getHeader('auth')
		);
	}

}

(new TestRequestParameterMapping())->run();
