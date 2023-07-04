<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Console\Command\RouteDumpCommand;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Contributte\Tester\Toolkit;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

// No endpoints
Toolkit::test(function (): void {
	$schema = new Schema();
	$command = new RouteDumpCommand($schema);

	$input = new ArgvInput();
	$output = new BufferedOutput();

	$command->run($input, $output);

	Assert::equal('No endpoints found', trim($output->fetch()));
});

// Some endpoints
Toolkit::test(function (): void {
	$schema = new Schema();

	$handler1 = new EndpointHandler('class1', 'method1');
	$endpoint1 = new Endpoint($handler1);
	$endpoint1->addMethod(Endpoint::METHOD_GET);
	$endpoint1->setMask('/example/foo');
	$schema->addEndpoint($endpoint1);

	$handler2 = new EndpointHandler('class1', 'method2');
	$endpoint2 = new Endpoint($handler2);
	$endpoint2->addMethod(Endpoint::METHOD_GET);
	$endpoint2->setMask('/example/{id}');
	$endpoint2->addParameter(new EndpointParameter('id'));
	$schema->addEndpoint($endpoint2);

	$handler3 = new EndpointHandler('class2', 'method1');
	$endpoint3 = new Endpoint($handler3);
	$endpoint3->addMethod(Endpoint::METHOD_GET);
	$endpoint3->setMask('/lorem-ipsum');
	$schema->addEndpoint($endpoint3);

	$command = new RouteDumpCommand($schema);

	$input = new ArgvInput();
	$output = new BufferedOutput();

	$command->run($input, $output);

	$result = trim(implode("\n", array_map(static fn (string $line): string => rtrim($line), explode("\n", $output->fetch()))));

	$expected = <<<'EOD'
Method Path          Handler         Parameters

 GET    /example/foo  class1  method1
 GET    /example/{id}         method2 path: id
 ────── ───────────── ─────── ─────── ──────────
 GET    /lorem-ipsum  class2  method1
EOD;

	Assert::equal($expected, $result);
});
