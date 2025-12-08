<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Throwable;

abstract class BaseApplication implements IApplication
{

	private const UNIQUE_HEADERS = [
		'content-type',
	];

	public function __construct(
		private readonly IErrorHandler $errorHandler,
	)
	{
	}

	public function run(): void
	{
		$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
		$this->runWith($request);
	}

	public function runWith(ApiRequest $request): void
	{
		try {
			$response = $this->dispatch($request);
		} catch (Throwable $exception) {
			$response = $this->errorHandler->handle(new DispatchError($exception, $request));
		}

		$this->sendResponse($response);
	}

	abstract protected function dispatch(ApiRequest $request): ApiResponse;

	protected function sendResponse(ApiResponse $response): void
	{
		$httpHeader = sprintf(
			'HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		);

		header($httpHeader, true, $response->getStatusCode());

		foreach ($response->getHeaders() as $name => $values) {
			$replace = in_array(strtolower($name), self::UNIQUE_HEADERS, true);

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
