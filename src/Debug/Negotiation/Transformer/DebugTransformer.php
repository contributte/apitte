<?php declare(strict_types = 1);

namespace Apitte\Debug\Negotiation\Transformer;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Negotiation\Transformer\AbstractTransformer;
use GuzzleHttp\Psr7\Utils;
use Tracy\Debugger;

class DebugTransformer extends AbstractTransformer
{

	public function __construct(
		private readonly int $maxDepth = 10,
		private readonly int $maxLength = 1500,
	)
	{
	}

	/**
	 * @param mixed[] $context
	 */
	public function transform(ApiRequest $request, ApiResponse $response, array $context = []): ApiResponse
	{
		Debugger::$maxDepth = $this->maxDepth;
		Debugger::$maxLength = $this->maxLength;

		$tmp = clone $response;

		$response = $response->withHeader('Content-Type', 'text/html')
			->withBody(Utils::streamFor())
			->withStatus(599);

		$response->getBody()->write(Debugger::dump($tmp, true));

		if (isset($context['exception'])) {
			$response->getBody()->write(Debugger::dump($context['exception'], true));
		}

		return $response;
	}

}
