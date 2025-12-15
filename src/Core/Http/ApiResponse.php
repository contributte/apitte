<?php declare(strict_types = 1);

namespace Apitte\Core\Http;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Negotiation\Http\AbstractEntity;
use Contributte\Psr7\ProxyResponse;

/**
 * Tiny wrapper for PSR-7 ResponseInterface
 */
class ApiResponse extends ProxyResponse
{

	public const S100_CONTINUE = 100;
	public const S101_SWITCHING_PROTOCOLS = 101;
	public const S102_PROCESSING = 102;
	public const S200_OK = 200;
	public const S201_CREATED = 201;
	public const S202_ACCEPTED = 202;
	public const S203_NON_AUTHORITATIVE_INFORMATION = 203;
	public const S204_NO_CONTENT = 204;
	public const S205_RESET_CONTENT = 205;
	public const S206_PARTIAL_CONTENT = 206;
	public const S207_MULTI_STATUS = 207;
	public const S208_ALREADY_REPORTED = 208;
	public const S226_IM_USED = 226;
	public const S300_MULTIPLE_CHOICES = 300;
	public const S301_MOVED_PERMANENTLY = 301;
	public const S302_FOUND = 302;
	public const S303_SEE_OTHER = 303;
	public const S303_POST_GET = 303;
	public const S304_NOT_MODIFIED = 304;
	public const S305_USE_PROXY = 305;
	public const S307_TEMPORARY_REDIRECT = 307;
	public const S308_PERMANENT_REDIRECT = 308;
	public const S400_BAD_REQUEST = 400;
	public const S401_UNAUTHORIZED = 401;
	public const S402_PAYMENT_REQUIRED = 402;
	public const S403_FORBIDDEN = 403;
	public const S404_NOT_FOUND = 404;
	public const S405_METHOD_NOT_ALLOWED = 405;
	public const S406_NOT_ACCEPTABLE = 406;
	public const S407_PROXY_AUTHENTICATION_REQUIRED = 407;
	public const S408_REQUEST_TIMEOUT = 408;
	public const S409_CONFLICT = 409;
	public const S410_GONE = 410;
	public const S411_LENGTH_REQUIRED = 411;
	public const S412_PRECONDITION_FAILED = 412;
	public const S413_REQUEST_ENTITY_TOO_LARGE = 413;
	public const S414_REQUEST_URI_TOO_LONG = 414;
	public const S415_UNSUPPORTED_MEDIA_TYPE = 415;
	public const S416_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	public const S417_EXPECTATION_FAILED = 417;
	public const S421_MISDIRECTED_REQUEST = 421;
	public const S422_UNPROCESSABLE_ENTITY = 422;
	public const S423_LOCKED = 423;
	public const S424_FAILED_DEPENDENCY = 424;
	public const S426_UPGRADE_REQUIRED = 426;
	public const S428_PRECONDITION_REQUIRED = 428;
	public const S429_TOO_MANY_REQUESTS = 429;
	public const S431_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	public const S451_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	public const S500_INTERNAL_SERVER_ERROR = 500;
	public const S501_NOT_IMPLEMENTED = 501;
	public const S502_BAD_GATEWAY = 502;
	public const S503_SERVICE_UNAVAILABLE = 503;
	public const S504_GATEWAY_TIMEOUT = 504;
	public const S505_HTTP_VERSION_NOT_SUPPORTED = 505;
	public const S506_VARIANT_ALSO_NEGOTIATES = 506;
	public const S507_INSUFFICIENT_STORAGE = 507;
	public const S508_LOOP_DETECTED = 508;
	public const S510_NOT_EXTENDED = 510;
	public const S511_NETWORK_AUTHENTICATION_REQUIRED = 511;

	/** @var mixed[] */
	protected array $attributes = [];

	public function hasAttribute(string $name): bool
	{
		return array_key_exists($name, $this->attributes);
	}

	public function getAttribute(string $name, mixed $default = null): mixed
	{
		if (!$this->hasAttribute($name)) {
			if (func_num_args() < 2) {
				throw new InvalidStateException(sprintf('No attribute "%s" found', $name));
			}

			return $default;
		}

		return $this->attributes[$name];
	}

	/**
	 * @return mixed[]
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * @return static
	 */
	public function withAttribute(string $name, mixed $value): self
	{
		$new = clone $this;
		$new->attributes[$name] = $value;

		return $new;
	}

	public function getEntity(): ?AbstractEntity
	{
		$entity = $this->getAttribute(ResponseAttributes::ATTR_ENTITY, null);

		return $entity instanceof AbstractEntity ? $entity : null;
	}

	/**
	 * @return static
	 */
	public function withEntity(AbstractEntity $entity): self
	{
		return $this->withAttribute(ResponseAttributes::ATTR_ENTITY, $entity);
	}

	public function getEndpoint(): ?Endpoint
	{
		$endpoint = $this->getAttribute(ResponseAttributes::ATTR_ENDPOINT, null);

		return $endpoint instanceof Endpoint ? $endpoint : null;
	}

	/**
	 * @return static
	 */
	public function withEndpoint(Endpoint $endpoint): self
	{
		return $this->withAttribute(ResponseAttributes::ATTR_ENDPOINT, $endpoint);
	}

}
