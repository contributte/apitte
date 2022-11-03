<?php declare(strict_types = 1);

namespace Apitte\Presenter;

use Apitte\Core\Application\IApplication;
use Apitte\Core\Http\ApiRequest;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Psr7\Psr7UriFactory;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\CallbackResponse;
use Nette\Http\Request as HttpRequest;

class ApiPresenter implements IPresenter
{

	private IApplication $application;

	private HttpRequest $request;

	public function __construct(IApplication $application, HttpRequest $request)
	{
		$this->application = $application;
		$this->request = $request;
	}

	public function run(Request $request): Response
	{
		$url = $this->request->getUrl();

		$psrRequest = Psr7ServerRequestFactory::fromNette($this->request)
			->withUri(Psr7UriFactory::fromNette($url));
		$psrRequest = new ApiRequest($psrRequest);

		return new CallbackResponse(function () use ($psrRequest): void {
			$this->application->runWith($psrRequest);
		});
	}

}
