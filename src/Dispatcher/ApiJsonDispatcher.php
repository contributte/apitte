<?php

namespace Apitte\Core\Dispatcher;

use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiJsonDispatcher extends ApiDispatcher
{

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
	{
		$response = $response->withStatus(404);
		$response->getBody()->write(Json::encode(['error' => 'No matched route by given URL']));

		return $response;
	}

}
