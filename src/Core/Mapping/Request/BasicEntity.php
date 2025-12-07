<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Mapping\TReflectionProperties;
use Apitte\Core\Schema\Endpoint;
use Nette\Utils\JsonException;
use TypeError;

/**
 * @template TKey of string|int
 * @template TValue of mixed
 * @extends AbstractEntity<TKey, TValue>
 */
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
	 * @return BasicEntity<TKey, TValue>|null
	 */
	public function fromRequest(ApiRequest $request): ?IRequestEntity
	{
		if (in_array($request->getMethod(), [Endpoint::METHOD_POST, Endpoint::METHOD_PUT, Endpoint::METHOD_PATCH], true)) {
			return $this->fromBodyRequest($request);
		}

		if (in_array($request->getMethod(), [Endpoint::METHOD_GET, Endpoint::METHOD_DELETE, Endpoint::METHOD_HEAD], true)) {
			return $this->fromGetRequest($request);
		}

		return null;
	}

	/**
	 * @param array<TKey, TValue> $data
	 * @return static<TKey, TValue>
	 */
	public function factory(array $data): self
	{
		$inst = new static();

		// Fill properties with real data
		$properties = $inst->getRequestProperties();
		foreach ($properties as $property) {
			/** @var TKey $propName */
			$propName = $property['name'];
			if (!array_key_exists($propName, $data)) {
				continue;
			}

			$value = $data[$propName];

			// Normalize & convert value (only not null values)
			if ($value !== null) {
				$value = $this->normalize($propName, $value);
			}

			// Fill single property
			try {
				$propNameStr = (string) $propName;
				if (property_exists($inst, $propNameStr)) {
					$ref = new \ReflectionProperty($inst, $propNameStr);
					$wasAccessible = $ref->isPublic();
					if (!$wasAccessible) {
						$ref->setAccessible(true);
					}
					$ref->setValue($inst, $value);
					if (!$wasAccessible) {
						$ref->setAccessible(false);
					}
				} elseif (method_exists($inst, '__set')) {
					$inst->__set($propName, $value);
				}
			} catch (TypeError) {
				// do nothing, entity will be invalid if something is missing and ValidationException will be thrown
			}
		}

		return $inst;
	}

	/**
	 * @param TKey $property
	 * @param TValue $value
	 * @return TValue
	 */
	protected function normalize(int|string $property, mixed $value): mixed
	{
		return $value;
	}

	/**
	 * @return static<TKey, TValue>
	 */
	protected function fromBodyRequest(ApiRequest $request): self
	{
		try {
			$body = (array) $request->getJsonBodyCopy(true);
		} catch (JsonException $ex) {
			throw new ClientErrorException('Invalid json data', 400, $ex);
		}

		return $this->factory($body);
	}

	/**
	 * @return static<TKey, TValue>
	 */
	protected function fromGetRequest(ApiRequest $request): self
	{
		return $this->factory($request->getQueryParams());
	}

}
