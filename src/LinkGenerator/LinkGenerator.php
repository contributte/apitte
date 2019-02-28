<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidLinkException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestScopeStorage;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Apitte\Core\UI\Controller\IController;
use Psr\Http\Message\UriInterface;
use ReflectionClass;

class LinkGenerator
{

	/** @var Schema */
	private $schema;

	/** @var RequestScopeStorage */
	private $requestScopeStorage;

	/** @var string[] */
	private $classCache = [];

	/** @var Endpoint[][] */
	private $endpointCache = [];

	/** @var array[] of module => splitted mask */
	private $mapping = [
		'*' => ['', '*\\', '*Controller'],
	];

	public function __construct(Schema $schema, RequestScopeStorage $requestScopeStorage)
	{
		$this->schema = $schema;
		$this->requestScopeStorage = $requestScopeStorage;
	}

	/**
	 * @param string  $destination "[[[module:]controller:]action]"
	 * @param mixed[] $parameters
	 * @throws InvalidLinkException
	 */
	public function link(string $destination, array $parameters = []): string
	{
		if (!preg_match('~^([\w:]+):(\w*+)(#.*)?()\z~', $destination, $m)) {
			throw new InvalidLinkException(sprintf('Invalid link destination "%s".', $destination));
		}

		[, $controller, $method, $fragment] = $m;

		$class = $this->getControllerClass($controller);
		$endpoint = $this->findEndpointByClassAndMethod($class, $method);

		return $this->buildUrl($endpoint, $parameters, $fragment);
	}

	private function getBaseUri(): string
	{
		/** @var UriInterface|null $uri */
		$uri = $this->requestScopeStorage->load('uri');

		if ($uri === null) {
			return '';
		}

		return $uri->getHost();
	}

	/**
	 * @todo - default values (missing implementation in EndpointParameter)
	 * @todo - check parameter types
	 * @todo - this method needs endpoint parameters defined - by default are optional and could be empty even if path or query accept some parameters
	 */
	private function buildUrl(Endpoint $endpoint, array $parameters, string $fragment): string
	{
		$pathParameters = [];
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_PATH) as $parameter) {
			$name = $parameter->getName();
			if (isset($parameters[$name])) {
				$this->checkParameterType($parameter, $parameters[$name]);
				$pathParameters[$name] = $parameters[$name];
				unset($parameters[$name]);
			} elseif (false) {
				//TODO - default value
			} elseif ($parameter->isAllowEmpty()) {
				$pathParameters[$name] = ''; //TODO - is empty string enough?
			} else {
				$handler = $endpoint->getHandler();
				throw new InvalidArgumentException(sprintf(
					'Cannot generate url for method "%s:%s", required parameter "%s" is missing.',
					$handler->getClass(),
					$handler->getClass(),
					$name
				));
			}
		}

		$queryParameters = [];
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_QUERY) as $parameter) {
			$name = $parameter->getName();
			if (isset($parameters[$name])) {
				$this->checkParameterType($parameter, $parameters[$name]);
				$queryParameters[$name] = $parameters[$name];
				unset($parameters[$name]);
			} elseif (false) {
				//TODO - default value
			} elseif (!$parameter->isAllowEmpty()) {
				$handler = $endpoint->getHandler();
				throw new InvalidArgumentException(sprintf(
					'Cannot generate url for method "%s:%s", required parameter "%s" is missing.',
					$handler->getClass(),
					$handler->getClass(),
					$name
				));
			}
		}

		//TODO - not working without CoreMappingPlugin
		if ($parameters !== []) {
			throw new InvalidArgumentException(sprintf('Parameters "%s" are not defined in a path nor query.', implode(', ', array_keys($parameters))));
		}

		//TODO - check if all parameters in mask are replaced (needed only if EndpointParameters are optional - CoreMappingPlugin enforce it by RequestParameterValidation)
		$mask = preg_replace_callback('#{(.*?)}#',
			function ($match) use ($pathParameters) {
				return $pathParameters[$match[1]];
			},
			$endpoint->getMask());

		$query = http_build_query($queryParameters);

		return $this->getBaseUri() . $mask . $query . $fragment;
	}

	private function checkParameterType(EndpointParameter $parameter, $value): void
	{
		//TODO
		//	- should use parameter mappers? (inversely then current behavior of normalize)
		//	- user could pass true and it will by converted into 1 for url - no way, as as true is recognized only true and "true" by BooleanTypeMapper
		//	- user could also pass "true" - which seems stupid
		$parameter->getType();
		$parameter->isAllowEmpty();
		$parameter->isRequired(); //TODO - needed?
	}

	private function getControllerClass(string $name): string
	{
		if (isset($this->classCache[$name])) {
			return $this->classCache[$name];
		}

		if (!preg_match('#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*\z#', $name)) {
			throw new InvalidLinkException(sprintf('Controller name must be alphanumeric string, "%s" is invalid.', $name));
		}

		$class = $this->formatControllerClass($name);

		if (!class_exists($class)) {
			throw new InvalidLinkException(sprintf('Cannot load controller "%s", class "%s" was not found.', $name, $class));
		}

		$reflection = new ReflectionClass($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface(IController::class)) {
			throw new InvalidLinkException(sprintf('Cannot load controller "%s", class "%s" is not "%s" implementor.', $name, $class, IController::class));
		}

		if ($reflection->isAbstract()) {
			throw new InvalidLinkException(sprintf('Cannot load controller "%s", class "%s" is abstract.', $name, $class));
		}

		$this->classCache[$name] = $class;

		if ($name !== ($realName = $this->unformatControllerClass($class))) {
			trigger_error(sprintf('Case mismatch on controller name "%s", correct name is "%s".', $name, $realName), E_USER_WARNING);
			$name = $realName;
		}

		return $class;
	}

	private function findEndpointByClassAndMethod(string $class, string $method): Endpoint
	{
		if (isset($this->endpointCache[$class][$method])) {
			return $this->endpointCache[$class][$method];
		}

		$classFound = false;

		foreach ($this->schema->getEndpoints() as $endpoint) {
			$handler = $endpoint->getHandler();
			if ($handler->getClass() === $class && $handler->getMethod() === $method) {
				$this->endpointCache[$class][$method] = $endpoint;
				return $endpoint;
			}
			if ($handler->getClass() === $class) {
				$classFound = true;
			}
		}

		if ($classFound) {
			throw new InvalidStateException(sprintf('Controllers "%s" method "%s" is missing in schema.', $class, $method));
		}

		throw new InvalidStateException(sprintf('Controller "%s" is missing in schema.', $class));
	}

	/**
	 * Sets mapping as pairs [module => mask]
	 * @param mixed[] $mapping
	 * @return static
	 */
	public function setMapping(array $mapping): self
	{
		foreach ($mapping as $module => $mask) {
			if (is_string($mask)) {
				if (!preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#', $mask, $m)) {
					throw new InvalidStateException(sprintf('Invalid mapping mask "%s" for module "%s".', $mask, $module));
				}
				$this->mapping[$module] = [$m[1], $m[2] ?: '*Module\\', $m[3]];
			} elseif (is_array($mask) && count($mask) === 3) {
				$this->mapping[$module] = [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
			} else {
				throw new InvalidStateException(sprintf('Invalid mapping mask for module "%s".', $module));
			}
		}
		return $this;
	}

	private function formatControllerClass(string $controller): string
	{
		$parts = explode(':', $controller);
		$mapping = isset($parts[1], $this->mapping[$parts[0]])
			? $this->mapping[array_shift($parts)]
			: $this->mapping['*'];
		while ($part = array_shift($parts)) {
			$mapping[0] .= str_replace('*', $part, $mapping[$parts ? 1 : 2]);
		}
		return $mapping[0];
	}

	private function unformatControllerClass(string $class): ?string
	{
		foreach ($this->mapping as $module => $mapping) {
			$mapping = str_replace(['\\', '*'], ['\\\\', '(\w+)'], $mapping);
			if (preg_match("#^\\\\?$mapping[0]((?:$mapping[1])*)$mapping[2]\\z#i", $class, $matches)) {
				return ($module === '*' ? '' : $module . ':')
					. preg_replace("#$mapping[1]#iA", '$1:', $matches[1]) . $matches[3];
			}
		}
		return null;
	}

}
