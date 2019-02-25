<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Psr\Http\Message\ServerRequestInterface;

interface IApplication
{

	public function run(): void;

	public function runWith(ServerRequestInterface $request): void;

}
