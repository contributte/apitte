# Apitte/Core

## Content

- [Installation - how to register an extension](#installation)
- [Configuration - all options](#configuration)
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

## Configuration

```
extensions:
    api: Apitte\Core\DI\ApiExtension

api:
    debug: %debugMode%
```

By default, debug mode is detected from `%debugMode%` variable from Nette. Also there are default plugins `Apitte\Core\DI\Plugin\CoreSchemaPlugin` and `Apitte\Core\DI\Plugin\CoreServicesPlugin` loaded.

You can read more about plugins in the [next chapter](#plugins).

## Usage

### Controllers

Your job is to create a couple of controllers representing your API. Let's take a look at one.

```php
namespace App\Controllers;

use Apitte\Core\Annotation\Controller\Controller;
use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;

/**
 * @Controller
 * @ControllerPath("/hello")
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

You have to mark your controllers with `@Controller` annotation and also define `@ControllerPath`.

Each public method with annotations `@Path` and `@Method` will be added to our API scheme and will be triggered in propel request.

One more thing left, you have to define your controllers as services, to let `Apitte\Core\Handler\ServiceHandler` obtain propel handler. 

```yaml
services:
    - App\Controllers\HelloController
```

At the end, open your browser and locate to `localhost/<api-project>/hello/worldd`.

**Tip** The `@ControllerPath("/")` annotation with the `@Path("/")` annotation target to homepage, e.q. `localhost/<api-project>`.

### Request & Response

`Apitte\Core\Http\ApiRequest` & `Apitte\Core\Http\ApiResponse` implement the PSR-7 interfaces.  

## Plugins

Apitte is divided into many plugins which are connected to one single awesome unit. The main `apitte\core` package is strongly required.

Core plugins are:

- `CoreServicesPlugin` (enabled by default)
- `CoreSchemaPlugin`  (enabled by default)
- `CoreDecoratorPlugin` (optional)
- `CodeMappingPlugin` (optional)

Another available plugins are:

- [`apitte\debug`](https://github.com/apitte/debug) - adds debugging tools for developing
- [`apitte\middlewares`](https://github.com/apitte/middlewares) - adds support for middlewares, depends on `contributte\middlewares`
- [`apitte\negotiation`](https://github.com/apitte/negotiation) - adds support for varient content negotiations (.json, .debug, .csv, etc.)
- [`apitte\openapi`](https://github.com/apitte/openapi) - adds support for openapi and swagger
- [`apitte\events`](https://github.com/apitte/events) - [WIP] - adds support for symfony/event-dispatcher

### CoreDecoratorPlugin

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreDecoratorPlugin:
```

This plugin overrides default implementation of `IDispatcher` and allows to add request & response decorators. You can manage/update incoming request data or unify JSON response data via registered decorators.

Each **decorator** should be registered with tag `apitte.core.decorator`. 

Each decorator should provide `type` attribute:

- `dispatcher.before` - called before dispatching (before routing)
- `handle.before` - called before controller is trigged (after routing)
- `dispatcher.after` - called after dispatching (after handling)
- `dispatcher.exception` - called if exception has been occured (special)

Also you should define a priority for better sorting. Default is 10.

```yaml
services:
    decorator.request.json: 
        class: App\Model\JsonBodyDecorator
        tags: [apitte.core.decorator: [priority: 50, type: dispatcher.before]]

services:
    decorator.request.xml: 
        class: App\Model\XmlBodyDecorator
        tags: [apitte.core.decorator: [priority: 60, type: dispatcher.before]]
```

When the DIC is compiled, we have a 2 decorators, the first is `@decorator.request.json`, because it has priority
50 and the second is `@decorator.request.xml`. Both of them are called before dispatching.


### CodeMappingPlugin

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CodeMappingPlugin:
```

This plugin allows you to define new annotations.

```php
/**
 * @Path("/user/{id}")
 * @Method("GET")
 * @RequestParameters({
 *      @RequestParameter(name="id", type="int", description="My favourite user ID")
 * })
 */
public function detail(ApiRequest $request)
{
    $id = $request->getParameter('id');
    // $id === int
}
```

It converts request parameters to defined type. By default, you can use `int`, `float`, `string`. Or 
register more types.

```
api:
    plugins:
        Apitte\Core\DI\Plugin\CodeMappingPlugin:
            types:
                int: Apitte\Core\Mapping\Parameter\IntegerTypeMapper
                float: Apitte\Core\Mapping\Parameter\FloatTypeMapper
                string: Apitte\Core\Mapping\Parameter\StringTypeMapper
                special: App\MySpecialType
```

Don't forget to register default one, because filling of `types` overrides default types.

## Advanced

There are planty of options that might be configured.

### Middlewares

This API is mainly (but not required) based on [contributte/middlewares](https://github.com/contributte/middlewares). You should register also middleware extension in your config file.

```yaml
extensions:
    middlewares: Contributte\Middlewares\DI\MiddlewaresExtension
    api: Apitte\Core\DI\ApiExtension
```

### Resources

It's boring to register each controller one by one, let them register over the `ResourceExtension`. Install another [contributte package - contributte/di](https://github.com/contributte/di).

And define your resources.

```yaml
extensions:
    resource: Contributte\DI\Extension\ResourceExtension
    middlewares: Contributte\Middlewares\DI\MiddlewaresExtension
    api: Apitte\Core\DI\ApiExtension

resource:
    resources:
        App\Controllers\:
            # where the classes are stored
            paths: [%appDir%/controllers]
```

## Playground

I've made a repository with full applications for education.

Take a look: https://github.com/apitte/playground
