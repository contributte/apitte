<?php declare(strict_types = 1);

namespace Apitte\Console\Command;

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RouteDumpCommand extends Command
{

	private const TABLE_HEADER = ['Method', 'Path', 'Handler', ' ', 'Parameters'];

	/** @var string */
	protected static $defaultName = 'apitte:route:dump';

	/** @var Schema */
	private $schema;

	public function __construct(Schema $schema)
	{
		parent::__construct();

		$this->schema = $schema;
	}

	protected function configure(): void
	{
		$this->setName(self::$defaultName);
		$this->setDescription('Lists all endpoints registered in application');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$endpoints = $this->schema->getEndpoints();

		if ($endpoints === []) {
			$output->writeln('No endpoints found');

			return 0;
		}

		$table = new Table($output);
		$table->setHeaders(self::TABLE_HEADER);

		$style = new TableStyle();
		$style
			->setDefaultCrossingChar(' ')
			->setVerticalBorderChars(' ')
			->setHorizontalBorderChars('', '─')
			->setCellRowContentFormat('%s');
		$table->setStyle($style);

		/** @var Endpoint[][] $endpointsByHandler */
		$endpointsByHandler = [];
		foreach ($endpoints as $endpoint) {
			$endpointsByHandler[$endpoint->getHandler()->getClass()][] = $endpoint;
		}

		foreach ($endpointsByHandler as $groupedEndpoints) {
			$previousClass = null;

			foreach ($groupedEndpoints as $endpoint) {
				$handler = $endpoint->getHandler();
				$currentClass = $class = $handler->getClass();

				if ($previousClass === $class) {
					$currentClass = '';
				}

				$table->addRow([
					sprintf(
						'<fg=cyan>%s</>',
						implode('|', $endpoint->getMethods())
					),
					$endpoint->getMask(),
					$currentClass,
					$handler->getMethod(),
					$this->formatParameters($endpoint->getParameters()),
				]);

				$previousClass = $class;
			}

			if ($groupedEndpoints !== end($endpointsByHandler)) {
				$table->addRow(new TableSeparator());
			}
		}

		$table->render();

		return 0;
	}

	/**
	 * @param EndpointParameter[] $parameters
	 */
	private function formatParameters(array $parameters): string
	{
		$paramsByIn = [];

		foreach ($parameters as $parameter) {
			$paramsByIn[$parameter->getIn()][] = $parameter->getName();
		}

		ksort($paramsByIn);

		$result = '';

		foreach ($paramsByIn as $in => $params) {
			$result .= sprintf('<fg=cyan>%s</>', $in) . ': ' . implode(', ', $params);
			if ($params !== end($paramsByIn)) {
				$result .= ' | ';
			}
		}

		return $result;
	}

}
