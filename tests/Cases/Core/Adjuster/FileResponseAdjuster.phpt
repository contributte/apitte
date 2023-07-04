<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Adjuster\FileResponseAdjuster;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

Toolkit::test(function (): void {
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$response = FileResponseAdjuster::adjust($response, $response->getBody(), 'filename');

	Assert::same(
		[
			'Content-Type' => ['application/octet-stream'],
			'Content-Description' => ['File Transfer'],
			'Content-Transfer-Encoding' => ['binary'],
			'Content-Disposition' => ['attachment; filename="filename"; filename*=utf-8\'\'filename'],
			'Expires' => ['0'],
			'Cache-Control' => ['must-revalidate, post-check=0, pre-check=0'],
			'Pragma' => ['public'],
			'Content-Length' => ['0'],
		],
		$response->getHeaders()
	);
});
