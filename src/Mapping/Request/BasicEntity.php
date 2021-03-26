<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Mapping\TReflectionProperties;
use Apitte\Core\Schema\Endpoint;

abstract class BasicEntity extends AbstractEntity
{

	use TReflectionProperties;

	/**
	 * @return mixed[]
	 */
	public function getRequestProperties(): array
	{
		return $this->getProperties();
	}

	/**
	 * @return BasicEntity|null
	 */
	public function fromRequest(ApiRequest $request): ?IRequestEntity
	{
		if (in_array($request->getMethod(), [Endpoint::METHOD_POST, Endpoint::METHOD_PUT, Endpoint::METHOD_PATCH], true)) {
			return $this->fromBodyRequest($request);
		}

		if (in_array($request->getMethod(), [Endpoint::METHOD_GET, Endpoint::METHOD_DELETE], true)) {
			return $this->fromGetRequest($request);
		}

		return null;
	}

	/**
	 * @param mixed[] $data
	 * @return static
	 */
	public function factory(array $data): self
	{
		$inst = new static();

		// Fill properties with real data
		$properties = $inst->getRequestProperties();
		foreach ($properties as $property) {
			if (!array_key_exists($property['name'], $data)) {
				continue;
			}

			$value = $data[$property['name']];

			// Normalize & convert value (only not null values)
			if ($value !== null) {
				$value = $this->normalize($property['name'], $value);
			}

			// Fill single property
			$inst->{$property['name']} = $value;
		}

		return $inst;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize(string $property, $value)
	{
		return $value;
	}

	/**
	 * @return static
	 */
	protected function fromBodyRequest(ApiRequest $request): self
	{
		return $this->factory((array) $request->getJsonBody(true));
	}

	/**
	 * @return static
	 */
	protected function fromGetRequest(ApiRequest $request): self
	{
		return $this->factory($request->getQueryParams());
	}

}
