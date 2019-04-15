<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7Response;
use Contributte\Psr7\Psr7ServerRequestFactory;

class Application implements IApplication
{

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
			foreach ($values as $value) {
				// never send multiple content-type headers
				if (preg_match('/content-type/i', $name)) {
					header(sprintf('%s: %s', $name, $value));
				}
				else {
					header(sprintf('%s: %s', $name, $value), false);
				}
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
