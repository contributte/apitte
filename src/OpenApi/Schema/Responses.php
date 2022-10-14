<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Responses
{

	/** @var Response[]|Reference[] */
	private array $responses = [];

	/**
	 * @param Response|Reference $response
	 */
	public function setResponse(string $key, $response): void
	{
		$this->responses[$key] = $response;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Responses
	{
		$responses = new Responses();
		foreach ($data as $key => $responseData) {
			if (isset($responseData['$ref'])) {
				$responses->setResponse((string) $key, new Reference($responseData['$ref']));
			} else {
				$responses->setResponse((string) $key, Response::fromArray($responseData));
			}
		}

		return $responses;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		foreach ($this->responses as $key => $response) {
			if ($key === 'default') {
				continue;
			}

			$data[$key] = $response->toArray();
		}

		// Default response last
		if (isset($this->responses['default'])) {
			$data['default'] = $this->responses['default']->toArray();
		}

		return $data;
	}

}
