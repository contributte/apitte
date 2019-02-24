# Router

Checks if an [endpoint](endpoints.md) from [schema](schema.md) matches request.

> Routing don't support any magic filters or canonization like e.g. [nette/routing](https://github.com/nette/routing/) do
> as api routing should be machine-readable and describable in a [schema](schema.md).

## SimpleRouter

Default implementation of router which matches endpoint by URI and by HTTP method

Requires each endpoint to have an unique combination of URI and HTTP method.
So you could have two endpoints with same URI which differs only in allowed HTTP method.

If an endpoint for given URI exists but not for given HTTP method then `405 Method Not Allowed` is returned.

### Request attributes

SimpleRouter adds into request some useful info

```php
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\Schema;

// Could be used for authentication of only some endpoints in request decorator
/** @var Endpoint $endpoint Schema of matched endpoint */
$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

/** @var mixed $match All matched parts of url */
$match = $request->getAttribute(RequestAttributes::ATTR_ROUTER);

/** @var mixed[] $parameters All raw (not mapped) request parameters from path and query */
$parameters = $request->getAttribute(RequestAttributes::ATTR_PARAMETERS);
```
