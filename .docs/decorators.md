# Decorators

Decorators are used for transformations of request before it is passed into endpoint and
for transformations of response after it is returned from endpoint.

They could e.g. add some useful data, transform them or handle request globally (e.g. for authorization).

## Setup

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreDecoratorPlugin:
```

> Decorator plugin overrides [JsonDispatcher](dispatcher.md#jsondispatcher) with [DecoratedDispatcher](dispatcher.md#decorateddispatcher)

## Register decorators

```yaml
services:
    decorator.request.authentication:
        class: App\Api\Decorator\ExampleResponseDecorator
        tags: [apitte.core.decorator: [priority: 50]]
```

Each decorator could have tag `apitte.core.decorator` with `priority` defined.

- Decorator with lowest `priority` number is called first.
- Default `priority` is 10

## Request decorators

Add some data to request or return response through `EarlyReturnResponseException`.

### RequestParameterDecorator and RequestEntityDecorator

Enable mapping of request parameters and entities.

Registered with priorities `100` and `101`.

See [mapping](mapping.md) chapter for more info.

### Implementing request decorator

#### MetadataDecorator

```yaml
services:
    decorator.request.metadata:
        class: App\Api\Decorator\RequestMetadataDecorator
        tags: [apitte.core.decorator: [priority: 50]
```


```php
namespace App\Api\Decorator;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class RequestMetadataDecorator implements IRequestDecorator
{

    /**
     * @throws EarlyReturnResponseException If other request decorators and also deeper layers (endpoint) should be skipped
     */
    public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
    {
        // Do something with request (e.g. add useful attributes for endpoint)
        $request = $request->withAttribute('attributeName', 'attributeValue');

        return $request;
    }

}
```

#### AuthenticationDecorator

```yaml
services:
    decorator.request.authentication:
        class: App\Api\Decorator\RequestAuthenticationDecorator
        tags: [apitte.core.decorator: [priority: 1, type: handler.before]]
```

```php
namespace App\Api\Decorator;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use GuzzleHttp\Psr7\Utils;

class RequestAuthenticationDecorator implements IRequestDecorator
{

    /**
     * @throws EarlyReturnResponseException If other request decorators and also deeper layers (endpoint) should be skipped
     */
    public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
    {
        if ($userAuthenticationFailed) {
            $body = Utils::streamFor(json_encode([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid credentials, authentication failed.'
            ]));

            $response = $response
                ->withStatus(403)
                ->withBody($body);
            throw new EarlyReturnResponseException($response);
        }

        return $request;
    }

}
```

**Tip**

You could also authenticate only some endpoints thanks to [tags](endpoints.md#list-of-annotations) and [metadata](router.md#request-attributes) from `SimpleRouter`.

```php
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;

/** @var Endpoint $endpoint Schema of matched endpoint */
$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

if ($endpoint->hasTag('noAuthentication')) {
    // Don't authenticate
}
```

## Response decorators

Modify response returned from endpoint.

You could return response through `EarlyReturnResponseException` so other response decorators are not used.

### ResponseEntityDecorator

Transforms data into format requested in `Accept` header and in url suffix (`/api/v1/users.xml`)

See [negotiation](negotiation.md) chapter for details.

### Implementing response decorator

```php
namespace App\Api\Decorator;

use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ExampleResponseDecorator implements IResponseDecorator
{

    /**
     * @throws EarlyReturnResponseException If other response decorators should be skipped
     */
    public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        // Do something with response (e.g. transform data)
        return $response;
    }

}
```

## Exception decorators

Transforms error into response.

If an exception decorator return null instead of response then error is handled by internal [error handler](errors.md#error-handler)

### ResponseEntityDecorator

Transforms error into format requested in `Accept` header and in url suffix (`/api/v1/users.xml`)

See [negotiation](negotiation.md) chapter for details.

### Implementing exception decorator

```php
namespace App\Api\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Throwable;

class ExampleExceptionDecorator implements IErrorDecorator
{

    public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
    {
        $response = $this->errorToResponse($response, $error);
        return $response;
    }

}

```
