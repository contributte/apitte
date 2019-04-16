<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7Response;
use Contributte\Psr7\Psr7ServerRequestFactory;

class Application implements IApplication
{

	private const UNIQUE_HEADERS = [
		'content-type',
	];

	/** @var IDispatcher */
	private $dispatcher;

	public function __construct(IDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	public function run(): void
	{
		$request = new ApiRequest(Psr7ServerRequestFactory::fromGlobal());
		$this->runWith($request);
	}

	public function runWith(ApiRequest $request): void
	{
		$response = new ApiResponse(new Psr7Response());

		$response = $this->dispatcher->dispatch($request, $response);

		$httpHeader = sprintf(
			'HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		);

		header($httpHeader, true, $response->getStatusCode());

		foreach ($response->getHeaders() as $name => $values) {
			$replace = in_array(strtolower($name), self::UNIQUE_HEADERS, true) ? true : false;
			foreach ($values as $value) {
				header(sprintf('%s: %s', $name, $value), $replace);
			}
		}

		$stream = $response->getBody();

		if ($stream->isSeekable()) {
			$stream->rewind();
		}

		while (!$stream->eof()) {
			echo $stream->read(1024 * 8);
		}
	}

}
