<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestScopeStorage;
use Apitte\Core\Mapping\RequestParameterMapping;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;

/**
 * Link generator compatible with CoreMappingPlugin
 */
class StrictLinkGenerator extends BaseLinkGenerator
{

	/** @var RequestParameterMapping */
	private $requestParameterMapping;

	public function __construct(Schema $schema, RequestScopeStorage $requestScopeStorage, ControllerMapper $mapper, RequestParameterMapping $requestParameterMapping)
	{
		parent::__construct($schema, $requestScopeStorage, $mapper);
		$this->requestParameterMapping = $requestParameterMapping;
	}

	/**
	 * @param mixed[] $parameters
	 */
	protected function buildUrl(Endpoint $endpoint, array $parameters, string $fragment): string
	{
		$pathParameters = [];
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_PATH) as $parameterDefinition) {
			$name = $parameterDefinition->getName();
			if (isset($parameters[$name])) {
				$pathParameters[$name] = $this->checkAndReplaceParameterType($parameterDefinition, $parameters[$name]);
				unset($parameters[$name]);
			} elseif (false) { // phpcs:ignore
				//TODO - $parameter->hasDefaultValue() && ($parameter->isRequired || !$parameter->isAllowEmpty())
			} elseif ($parameterDefinition->isAllowEmpty() || !$parameterDefinition->isRequired()) {
				//TODO - path parameter emptiness should be checked in schema validation
				throw new InvalidArgumentException(sprintf(
					'Path parameter "%s" should not be empty. Please report to us if you have valid use case where it could be empty.',
					$parameterDefinition->getName()
				));
			} else {
				$handler = $endpoint->getHandler();
				throw new InvalidArgumentException(sprintf(
					'Cannot generate url for method "%s:%s", required parameter "%s" is missing.',
					$handler->getClass(),
					$handler->getMethod(),
					$name
				));
			}
		}

		$queryParameters = [];
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_QUERY) as $parameterDefinition) {
			$name = $parameterDefinition->getName();
			if (isset($parameters[$name])) {
				$queryParameters[$name] = $this->checkAndReplaceParameterType($parameterDefinition, $parameters[$name]);
				unset($parameters[$name]);
			} elseif (false) { // phpcs:ignore
				//TODO - $parameter->hasDefaultValue() && ($parameter->isRequired || !$parameter->isAllowEmpty())
			} elseif (!$parameterDefinition->isAllowEmpty() || $parameterDefinition->isRequired()) {
				$handler = $endpoint->getHandler();
				throw new InvalidArgumentException(sprintf(
					'Cannot generate url for method "%s:%s", required parameter "%s" is missing.',
					$handler->getClass(),
					$handler->getMethod(),
					$name
				));
			}
		}

		if ($parameters !== []) {
			throw new InvalidArgumentException(sprintf('Parameters "%s" are not defined in a path nor query.', implode(', ', array_keys($parameters))));
		}

		//TODO - check if all parameters in mask are replaced (needed only if path EndpointParameters are optional - CoreMappingPlugin enforce it by RequestParameterValidation)
		$mask = preg_replace_callback(
			'#{(.*?)}#',
			function ($match) use ($pathParameters) {
				return $pathParameters[$match[1]];
			},
			(string) $endpoint->getMask()
		);

		$query = http_build_query($queryParameters);

		return $this->getBaseUri() . $mask . $query . $fragment;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	private function checkAndReplaceParameterType(EndpointParameter $parameter, $value)
	{
		if ($parameter->isAllowEmpty() && in_array($value, [null, '', []], true)) {
			return '';
		}

		$type = $parameter->getType();
		$mapper = $this->requestParameterMapping->getMapper($type);

		if ($mapper === null) {
			throw new InvalidStateException(sprintf('Missing mapper for parameter of type "%s".', $type));
		}

		return $mapper->denormalize($value);
	}

}
