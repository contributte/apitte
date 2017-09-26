# API - Guidelines

## Content

- [Installation - how to register an extension](#installation)
- [Usage - controller showtime](#usage)
- [Plugins - apitte plugins](#plugins)
- [Advanced - complex configuration](#advanced)
- [Playground - real examples](#playground)

## Installation

Simpliest way to register this core API library is via [Nette\DI\CompilerExtension](https://api.nette.org/2.4/Nette.DI.CompilerExtension.html).

```
composer require apitte/core
```

```yaml
extensions:
    api: Apitte\Core\DI\ApiExtension
```

## Usage

### Controllers

Your job is to create a couple of controllers representing your API. Let's take a look at one.

```php
namespace App\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RootPath;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @RootPath("/hello")
 */
final class HelloController implements IController
{

    /**
     * @Path("/world")
     * @Method("GET")
     */
    public function index(ApiRequest $request, ApiResponse $response)
    {
        return $response->writeBody('Hello world!');
    }
}
```

This API by automatic look for all services which implements `Apitte\Core\UI\Controller\IController`. 
Then they are analyzed by annotations loader and `Apitte\Core\Schema\ApiSchema` is build.

You have to mark your controllers with `@Controller` annotation and also define `@RootPath`.

Each public method with annotations `@Path` and `@Method` will be added to our API scheme and will be triggered in propel request.

One more thing left, you have to define your controllers as services, to let `Apitte\Core\Handler\ServiceHandler` obtain propel handler. 

```yaml
services:
    - App\Controllers\HelloController
```

At the end, open your browser and locate to `localhost/<api-project>/hello/worldd`.

### Request & Response

`Apitte\Core\Http\ApiRequest` & `Apitte\Core\Http\ApiResponse` implement the PSR-7 interfaces.  

## Plugins

Apitte is considered into many plugins which are connected to one single awesome unit. The main `apitte\core` package is strongly required.

Another available plugins are:

- [`apitte\middlewares`](https://github.com/apitte/middlewares) - added support for middlewares, depends on `contributte\middlewares`
- [`apitte\negotiation`](https://github.com/apitte/negotiation) - added support for varient content negotiations (.json, .debug, .csv, etc.)
- [`apitte\mapping`](https://github.com/apitte/mapping) - added support for request parameters converting
- [`apitte\events`](https://github.com/apitte/events) - [WIP] - added support for symfony/event-dispatcher
- [`apitte\openapi`](https://github.com/apitte/openapi) - [WIP] - added support for openapi and swagger
- [`apitte\debug`](https://github.com/apitte/debug) - added debugging tools for developing

## Advanced

There are planty of options that might configured.

### Middlewares

This API is mainly (but not required) based on [contributte/middlewares](https://github.com/contributte/middlewares). You should register also middleware extension in your config file.

```yaml
extensions:
    middlewares: Contributte\Middlewares\DI\MiddlewaresExtension
    api: Apitte\Core\DI\ApiExtension
```

### Resources

It's boring to register each controller by one, let them be registered over resources. Install another [contributte package](https://github.com/contributte/di).

```
composer install contributte/di
```

And define your resource.

```yaml
extensions:
    resource: Contributte\DI\Extension\ResourceExtension
    middlewares: Contributte\Middlewares\DI\MiddlewaresExtension
    api: Apitte\Core\DI\ApiExtension

resource:
    resources:
        App\Controllers\:
            paths: [%appDir%/controllers]
```

## Playground

I've made a repository with full applications for education.

Take a look: https://github.com/apitte/playground
