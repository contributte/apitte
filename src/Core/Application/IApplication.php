<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Apitte\Core\Http\ApiRequest;

interface IApplication
{

	public function run(): void;

	public function runWith(ApiRequest $request): void;

}
