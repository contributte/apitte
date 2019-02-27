<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\RequestScopeStorage;
use Contributte\Psr7\Psr7Response;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application implements IApplication
{

	/** @var IDispatcher */
	private $dispatcher;

	/** @var RequestScopeStorage */
	private $requestScopeStorage;

	public function __construct(IDispatcher $dispatcher, RequestScopeStorage $requestScopeStorage)
	{
		$this->dispatcher = $dispatcher;
		$this->requestScopeStorage = $requestScopeStorage;
	}

	public function run(): void
	{
		$request = Psr7ServerRequestFactory::fromGlobal();
		$this->runWith($request);
	}

	public function runWith(ServerRequestInterface $request): void
	{
		$this->requestScopeStorage->save('uri', $request->getUri());

		$response = $this->dispatcher->dispatch($request, new Psr7Response());
		$this->sendResponse($response);

		$this->requestScopeStorage->clear();
	}

	protected function sendResponse(ResponseInterface $response): void
	{
		$httpHeader = sprintf(
			'HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		);

		header($httpHeader, true, $response->getStatusCode());

		foreach ($response->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				header(sprintf('%s: %s', $name, $value), false);
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
