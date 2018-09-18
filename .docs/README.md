# Apitte/Core

## Content

* [Installation - how to register an extension](#installation)
* [Configuration - all configurable options](#configuration)
* [Usage](#usage)
  + [Controllers](#controllers)
  + [Request & Response](#request---response)
* [Annotations - list of all annotations](#annotations)
* [Decorators](#decorators)
* [Plugins](#plugins)
  + [CoreDecoratorPlugin](#coredecoratorplugin)
    - [Default decorators](#default-decorators)
  + [CoreMappingPlugin](#coremappingplugin)
    - [Types](#types)
    - [Entity](#entity)
* [Bridges](#bridges)
  + [Middlewares](#middlewares)
  + [Resources](#resources)
* [Playground](#playground)

## Installation

Simplest way to register this core API library is via [Nette\DI\CompilerExtension](https://api.nette.org/2.4/Nette.DI.CompilerExtension.html).

```
composer require apitte/core
```

```yaml
extensions:
    api: Apitte\Core\DI\ApiExtension
```

## Configuration

```yaml
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
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
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

## Annotations

| Annotation           | Target | Attributes                                                                                                                                                                                | Description                                                                       |
|----------------------|--------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|
| `@Controller`        | Class  | none                                                                                                                                                                                      | Mark as as type `controller`.                                                     |
| `@ControllerId`      | Class  | value=`{a-z, A-Z, 0-9, _}`                                                                                                                                                                | Prefix all children methods ids with `id`.                                        |
| `@ControllerPath`    | Class  | value=`{a-z, A-Z, 0-9, -_/}`                                                                                                                                                              | Prefix all children methods paths with `path`.                                    |
| `@GroupId`           | Class  | value=`{a-z, A-Z, 0-9, _}`                                                                                                                                                                | Prefix all children methods ids with `id`. Can be set only on abstract class.     |
| `@GroupPath`         | Class  | value=`{a-z, A-Z, 0-9, -_/}`                                                                                                                                                              | Prefix all children methods paths with `path`. Can be set only on abstract class. |
| `@Id`                | Method | value=`{a-z, A-Z, 0-9, _}`                                                                                                                                                                | Set `id` to target method.                                                        |
| `@Method`            | Method | GET, POST, PUT, OPTION, DELETE, HEAD                                                                                                                                                      | Set `method` to target method.                                                    |
| `@Negotiations`      | Method | `@Negotiation`                                                                                                                                                                            | Group annotation for `@Negotiation`.                                              |
| `@Negotiation`       | Method | `suffix={string}`, `default={true/false}`, `renderer={string}`                                                                                                                            | Define negotiation mode to target method.                                         |
| `@Path`              | Method | `value={a-z, A-Z, 0-9, -_/{}}`                                                                                                                                                            | Set `path` to target method. A.k.a. URL path.                                     |
| `@RequestParameters` | Method | `@RequestParameter`                                                                                                                                                                       | Group annotation for `@RequestParameter`.                                         |
| `@RequestParameter`  | Method | `name={string}`, `type={scalar/string/int/float/bool/datetime}`, `description={string}`, `in={path/query/header/cookie}`, `required={true/false}`, `deprecated={true/false}`, `allowEmpty={true/false}` | Define dynamic typed parameter.                                                   |
| `@Tag`               | Method | `name={string}`, `value={mixed}`                                                                                                                                                          | Add `tag` to target method.                                                       |

## Decorators

#### FileResponseDecorator

- Transform response for simply send of file

```php
use Apitte\Core\Response\Decorator\FileResponseDecorator;

$decorator = new FileResponseDecorator();
$response = $decorator->decorate(ResponseInterface $response, StreamInterface $data, string $filename);
```

## Plugins

Apitte is divided into many plugins which are connected to one single awesome unit. The main `apitte\core` package is strongly required.

Core plugins are:

- [`CoreServicesPlugin`](https://github.com/apitte/core/blob/master/src/DI/Plugin/CoreServicesPlugin.php) (enabled by default)
- [`CoreSchemaPlugin`](https://github.com/apitte/core/blob/master/src/DI/Plugin/CoreSchemaPlugin.php)  (enabled by default)
- [`CoreDecoratorPlugin`](https://github.com/apitte/core/blob/master/src/DI/Plugin/CoreDecoratorPlugin.php) (optional)
- [`CoreMappingPlugin`](https://github.com/apitte/core/blob/master/src/DI/Plugin/CoreMappingPlugin.php) (optional)

Another available plugins are:

- [`apitte\debug`](https://github.com/apitte/debug) - adds debugging tools for developing
- [`apitte\middlewares`](https://github.com/apitte/middlewares) - adds support for middlewares, depends on [`contributte\middlewares`](https://github.com/contributte/middlewares)
- [`apitte\negotiation`](https://github.com/apitte/negotiation) - adds support for variant content negotiations (.json, .debug, .csv, etc.)
- [`apitte\openapi`](https://github.com/apitte/openapi) - adds support for openapi and swagger
- [`apitte\events`](https://github.com/apitte/events) - [WIP] - adds support for symfony/event-dispatcher (which is ported into nette via [`contributte\event-dispatcher`](https://github.com/contributte/event-dispatcher))

### CoreDecoratorPlugin

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreDecoratorPlugin:
```

This plugin overrides default implementation of `IDispatcher` and allows to add request & response decorators. You can manage/update incoming request data or unify JSON response data via registered decorators.

Each **decorator** should be registered with tag `apitte.core.decorator`.

Each decorator should provide `type` attribute:

- `handle.before` - called before controller method is triggered (after endpoint is matched in router)
- `handle.after` - called after controller method is triggered (after logic in controller)
- `dispatcher.exception` - called if exception has been occurred

Also you should define a priority for better sorting. Default is 10.

```yaml
services:
    decorator.request.json:
        class: App\Model\JsonBodyDecorator
        tags: [apitte.core.decorator: [priority: 50, type: handler.before]]

services:
    decorator.request.xml:
        class: App\Model\XmlBodyDecorator
        tags: [apitte.core.decorator: [priority: 60, type: handler.before]]
```

When the DIC is compiled, we have a 2 decorators, the first is `@decorator.request.json`, because it has priority
50 and the second is `@decorator.request.xml`. Both of them are called before dispatching.

#### Default decorators

These decorators are registered by default. Be careful about priorities.

| Plugin      | Class                        | Type              | Priority | Description                   |
|-------------|------------------------------|-------------------|----------|-------------------------------|
| core        | `RequestParametersDecorator` | `handler.before`  | 100      | Enable `@RequestParameter(s)` |
| core        | `RequestEntityDecorator`     | `handler.before`  | 101      | Enable `@RequestMapper`       |
| negotiation | `ResponseEntityDecorator`    | `handler.after `  | 500      | Enable `@ResponseMapper`<br>Converts response entity to different formats |

### CoreMappingPlugin

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
          types: []
          request:
            validator: Apitte\Core\Mapping\Validator\NullValidator
```

#### Types

This plugin allows you to define annotation @RequestParameter which validates and converts data type of GET parameters.

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

Available data types are `scalar`, `string`, `int`, `float`, `bool` and `datetime`.

- `scalar`
    - Tries to automatically convert value to boolean, int or float.
    - Returns string, if conversion is not possible.
- `string`
    - Simply returns given value.
- `int`
    - Converts value to int.
    - Could overflow to float if value is bigger than PHP could handle. If it is your case then replace `IntegerTypeMapper` with `StringTypeMapper`
- `float`
    - Converts value to float.
    - Accepts values which have decimals divided by comma `,` or dot `.`
- `bool`
    - Converts `'true'` and `'1'` to `true`
    - and `false` and `0` to `false`
- `datetime`
    - Converts value to DateTimeImmutable.
- Each of the data types could return null if @RequestParameter(allowEmpty=true)
- If conversion is not possible so API returns HTTP 400

You can override these types by your own implementation.

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
            types:
                scalar: Apitte\Core\Mapping\Parameter\ScalarTypeMapper
                string: Apitte\Core\Mapping\Parameter\StringTypeMapper
                int: Apitte\Core\Mapping\Parameter\IntegerTypeMapper
                float: Apitte\Core\Mapping\Parameter\FloatTypeMapper
                bool: Apitte\Core\Mapping\Parameter\BooleanTypeMapper
                datetime: Apitte\Core\Mapping\Parameter\DateTimeTypeMapper
```

#### Entity

##### RequestMapper

Let's try to picture you have a datagrid with many filter options. You can describe all options manually or
use value object, entity, for it. And it leads us to `@RequestMapper`.

We have some entity with described fields.

```php
namespace App\Controllers\Entity\Request;

use Apitte\Core\Mapping\Request\BasicEntity;

final class UserFilter extends BasicEntity
{

	/**  @var int */
	public $userId;

	/**  @var string */
	public $email;
}
```

And some endpoint with `@RequestMapper` annotation. There's a method `ApiRequest::getEntity()`, it gets
the entity from request attributes. So simple, right?

```php
/**
 * @Path("/filter")
 * @Method("GET")
 * @RequestMapper(entity="App\Controllers\Entity\Request\UserFilter")
 */
public function filter(ApiRequest $request)
{
    $entity = $request->getEntity();
    // $entity === UserFilter
}
```

There's a prepared validator for request entity, but it's disabled by default. You have to
pick the validator you want to.

```yaml
api:
    plugins:
        Apitte\Core\DI\Plugin\CoreMappingPlugin:
          request:
            # By default
            validator: Apitte\Core\Mapping\Validator\NullValidator

            # Support: @required
            validator: Apitte\Core\Mapping\Validator\BasicValidator

            # Symfony/Validator
            validator: Apitte\Core\Mapping\Validator\SymfonyValidator(@phpdoc.reader)
```

If you want use SymfonyValidator so also register PhpdocExtension

```yaml
extensions:
    phpdoc: Contributte\PhpDoc\DI\PhpDocExtension
```

Your entity could looks like this.

```php
final class UserFilter extends BasicEntity
{

	/**
	 * @var int
	 * @Assert\NotNull()
	 * @Assert\Type(
	 *     type="integer",
	 *     message="The value {{ value }} is not a valid {{ type }}."
	 * )
	 */
	public $userId;

}
```

##### ResponseMapper

`@ResponseMapper` is almost same as `@RequestMapper`. It just applies to response instead of request.

You could use it to for example to filter values or convert output to different format.

```php
namespace App\Controllers\Entity\Response;

use Apitte\Core\Mapping\Response\BasicEntity;

final class UserFilter extends BasicEntity
{

	/**  @var int */
	public $userId;

	/**  @var string */
	public $email;
}
```

```php
/**
 * @Path("/filter")
 * @Method("GET")
 * @ResponseMapper(entity="App\Controllers\Entity\Response\UserFilter")
 */
public function filter(ApiRequest $request)
{
    return $request->getEntity();
}
```

## Bridges

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
