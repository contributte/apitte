# Middlewares

Middlewares for Apitte.

Transform and validate request or early return response before it is handled by dispatcher.

## Setup

Install and register middlewares plugin.

```neon
api:
    plugins:
        Apitte\Middlewares\DI\MiddlewaresPlugin:
```

In `index.php` replace `Apitte\Core\Application\IApplication` with `Contributte\Middlewares\Application\IApplication`.

```php
// www/index.php

use Contributte\Middlewares\Application\IApplication;
use App\Bootstrap;

require __DIR__ . '/../vendor/autoload.php';

Bootstrap::boot()
    ->createContainer()
    ->getByType(IApplication::class)
    ->run();
```

In your **NEON** configuration file, register `MiddlewaresExtension` from [contributte/middlewares](https://github.com/contributte/middlewares) package.

```neon
extensions:
	middleware: Contributte\Middlewares\DI\MiddlewaresExtension

middleware:
	debug: %debugMode%
```

## Configuration

[TracyMiddleware](https://github.com/contributte/middlewares/blob/master/.docs/README.md#tracymiddleware) (with priority 100)
and [AutoBasePathMiddleware](https://github.com/contributte/middlewares/blob/master/.docs/README.md#autobasepathmiddleware) (with priority 200)
are registered by default, but you could disable them if you want.

```neon
api:
    plugins:
        Apitte\Middlewares\DI\MiddlewaresPlugin:
            tracy: true
            autobasepath: true
```

`Apitte\Middlewares\ApiMiddleware` which run whole Apitte application is registered with priority 500. Make sure there is no middleware with higher priority.

## Middlewares

If you want to add another middleware, just register a class with appropriate tags.

```neon
services:
    m1:
        factory: App\Api\Middleware\ExampleMiddleware
        tags: [middleware: [priority: 10]]
```

```php
namespace App\Api\Middleware;

use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExampleMiddleware implements IMiddleware
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        // Call next middleware in a row
        $response = $next($request, $response);
        // Return response
        return $response;
    }

}
```

See [contributte/middlewares](https://github.com/contributte/middlewares) documentation for more info and useful middlewares.

## Exception Handling

**Important note about `TryCatchMiddleware`:**

If you use `TryCatchMiddleware` from [contributte/middlewares](https://github.com/contributte/middlewares), be aware that it only catches exceptions thrown in **other middleware**, not exceptions thrown in controllers.

Exceptions thrown during controller execution are handled internally by `ApiMiddleware`, which wraps the dispatcher call in its own try-catch block. This means:

- `TryCatchMiddleware` catches exceptions from middleware running before or after `ApiMiddleware`
- `ApiMiddleware` catches and handles exceptions from the dispatcher (controller execution)
- Controller exceptions are processed by the internal [error handler](errors.md) or [exception decorators](decorators.md#exception-decorators)

If you need custom exception handling for controller errors, use [exception decorators](decorators.md#exception-decorators) or implement a custom [error handler](errors.md#error-handler)
