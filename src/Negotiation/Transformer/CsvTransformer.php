<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Transformer;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use RuntimeException;

class CsvTransformer extends AbstractTransformer
{

	/**
	 * Encode given data for response
	 *
	 * @param mixed[] $context
	 */
	public function transform(ApiRequest $request, ApiResponse $response, array $context = []): ApiResponse
	{
		if (isset($context['exception'])) {
			return $this->transformException($context['exception'], $request, $response);
		}

		return $this->transformResponse($request, $response);
	}

	protected function transformException(ApiException $exception, ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$content = sprintf('Exception occurred with message "%s"', $exception->getMessage());
		$response->getBody()->write($content);

		// Setup content type
		return $response
			->withStatus($exception->getCode())
			->withHeader('Content-Type', 'text/plain');
	}

	protected function transformResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$content = $this->convert($this->getEntity($response)->getData());
		$response->getBody()->write($content);

		// Setup content type
		return $response
			->withHeader('Content-Type', 'text/plain');
	}

	/**
	 * @param mixed[][] $rows
	 */
	private function convert(array $rows, string $delimiter = ',', string $enclosure = '"'): string
	{
		$fp = fopen('php://temp', 'r+');

		if ($fp === false) {
			throw new RuntimeException('IO exception');
		}

		foreach ($rows as $row) {
			foreach ($row as $item) {
				if (is_array($item) || !is_scalar($item)) {
					return 'CSV need flat array';
				}
			}

			fputcsv($fp, $row, $delimiter, $enclosure);
		}

		rewind($fp);
		$data = fread($fp, 1048576);
		fclose($fp);

		return rtrim((string) $data, "\n");
	}

}
